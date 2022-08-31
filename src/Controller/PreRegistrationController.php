<?php

namespace App\Controller;

use App\Entity\PreRegistrationFormField;
use App\Entity\RecruitmentSession;
use App\Entity\Result;
use App\Entity\User;
use App\Form\PreRegistrationFormFieldType;
use App\Form\PreRegistrationFormType;
use App\service\UploaderHelper;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class PreRegistrationController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('admin/preregistration', name: 'app_pre_registration')]
    public function index(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $fields = [];
        if ($recruitmentSession) {
            $start = $this->em->getRepository(PreRegistrationFormField::class)->findOneBy(['recruitmentSession' => $recruitmentSession, 'previousFormField' => NULL]);
            if ($start != null) {
                $fields[] = $start;
                while ($start->getNextFormField() != null) {
                    $fields[] = $start->getNextFormField();
                    $start = $start->getNextFormField();
                }
            }
        }
        $field = new PreRegistrationFormField();
        $fieldForm = $this->createForm(PreRegistrationFormFieldType::class, $field);
        $fieldForm->handleRequest($request);
        if ($fieldForm->isSubmitted() && $fieldForm->isValid()) {
            if (in_array($fieldForm['type']->getData(), ['select', 'checkbox', 'radio']) && count($fieldForm['preRegistrationFormFieldOptions']->getData()) < 2) {
                $this->addFlash('error', 'you should enter at least 2 options.');
                $html = $this->renderView('pre_registration/_field_form.html.twig', [
                    'recruitmentSession' => $recruitmentSession,
                    'fieldForm' => $fieldForm->createView()
                ]);
                return new Response($html, 400);
            } else {
                $start = $this->em->getRepository(PreRegistrationFormField::class)->findOneBy(['recruitmentSession' => $recruitmentSession, 'previousFormField' => NULL]);
                if ($start != null) {
                    while ($start->getNextFormField() != null) {
                        $start = $start->getNextFormField();
                    }
                }
                $field->setRecruitmentSession($recruitmentSession);
                //check if next not null
                $field->setPreviousFormField($start);
                $this->em->persist($field);
                $this->em->flush($field);
                if ($request->isXmlHttpRequest()) {
                    $fieldItem = $this->renderView('pre_registration/_field_item.html.twig', [
                        'field' => $field
                    ]);

                    $field = new PreRegistrationFormField();
                    $fieldForm = $this->createForm(PreRegistrationFormFieldType::class, $field);
                    $fieldFormView = $this->renderView('pre_registration/_field_form.html.twig', [
                        'recruitmentSession' => $recruitmentSession,
                        'fieldForm' => $fieldForm->createView()
                    ]);
                    return new JsonResponse(['fieldFormView' => $fieldFormView, 'fieldItem' => $fieldItem], 200);
                }

                return $this->redirectToRoute('app_pre_registration');
            }
        } else {
            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView('pre_registration/_field_form.html.twig', [
                    'recruitmentSession' => $recruitmentSession,
                    'fieldForm' => $fieldForm->createView()
                ]);
                return new Response($html, 400);
            }
        }

        return $this->render('pre_registration/index.html.twig', [
            'fields' => $fields,
            'recruitmentSession' => $recruitmentSession,
            'fieldForm' => $fieldForm->createView()
        ]);
    }

    #[Route('admin/preregistration/get/field', name: 'app_pre_registration_get_field', methods: ['post'])]
    public function getFieldData(Request $request): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $field = $this->em->getRepository(PreRegistrationFormField::class)->find($request->request->get('fieldId'));
        $fieldForm = $this->createForm(PreRegistrationFormFieldType::class, $field)
            ->add('id', HiddenType::class, [
                'mapped' => true
            ]);
        $fieldForm->handleRequest($request);
        return $this->render('pre_registration/_edit_field_form.html.twig', [
            'recruitmentSession' => $recruitmentSession,
            'fieldForm' => $fieldForm->createView(),
            'fieldId' => $field->getId()
        ]);

    }

    #[Route('admin/preregistration/edit/field', name: 'app_pre_registration_edit_field', methods: ['post'])]
    public function editField(Request $request): Response
    {
        $fieldId = $request->request->all()['pre_registration_form_field']['id'];
        $field = $this->em->getRepository(PreRegistrationFormField::class)->find($fieldId);
        $originalOptions = new ArrayCollection();
        foreach ($field->getPreRegistrationFormFieldOptions() as $option) {
            $originalOptions->add($option);
        }
        $fieldForm = $this->createForm(PreRegistrationFormFieldType::class, $field)
            ->add('id', HiddenType::class, [
                'mapped' => false
            ]);
        $fieldForm->handleRequest($request);

        if ($fieldForm->isSubmitted() && $fieldForm->isValid()) {
            if (in_array($fieldForm['type']->getData(), ['select', 'checkbox', 'radio']) && count($fieldForm['preRegistrationFormFieldOptions']->getData()) < 2) {
                $this->addFlash('error', 'you should enter at least 2 options.');
                $html = $this->renderView('pre_registration/_edit_field_form.html.twig', [
                    'recruitmentSession' => $field->getRecruitmentSession(),
                    'fieldForm' => $fieldForm->createView(),
                    'fieldId' => $field->getId()
                ]);
                return new Response($html, 400);
            }
            foreach ($originalOptions as $option) {
                $this->em->remove($option);
            }
            if (!in_array($field->getType(), ['select', 'checkbox', 'radio'])) {
                foreach ($field->getPreRegistrationFormFieldOptions() as $option) {
                    $field->removePreRegistrationFormFieldOption($option);
                }
                $this->em->flush();
            }
            $this->em->persist($field);
            $this->em->flush();
            if ($request->isXmlHttpRequest()) {
                $fieldItem = $this->renderView('pre_registration/_field_item.html.twig', [
                    'field' => $field
                ]);

                return new Response($fieldItem, 200);
            }
            return $this->redirectToRoute('app_pre_registration');
        }
        return $this->redirectToRoute('app_pre_registration');
    }

    #[Route('admin/preregistration/field/remove/{field}', name: 'app_pre_registration_remove_field')]
    public function removeField(PreRegistrationFormField $field): Response
    {
        $recruitmentSessionId = $field->getRecruitmentSession()->getId();
        $next = $field->getNextFormField() != null ? $this->em->getRepository(PreRegistrationFormField::class)->find($field->getNextFormField()->getId()) : null;
        $previous = $field->getPreviousFormField() != null ? $this->em->getRepository(PreRegistrationFormField::class)->find($field->getPreviousFormField()->getId()) : null;
        $field->setPreviousFormField(null);
        $field->setNextFormField(null);
        $this->em->remove($field);
        $this->em->flush();

        if ($previous)
            $previous->setNextFormField($next);
        if ($next)
            $next->setPreviousFormField($previous);

        $this->em->flush();
        return $this->redirectToRoute('app_pre_registration');
    }

    #[Route('admin/preregistration/save/fields/order', name: 'app_pre_registration_question_order')]
    public function saveFieldsOrder(Request $request): Response
    {
        $order = $request->request->all()["order"];
        for ($i = 0; $i < count($order); $i++) {
            $field = $this->em->getRepository(PreRegistrationFormField::class)->findOneBy((['id' => $order[$i]]));
            $field->setNextFormField(NULL);
            $field->setPreviousFormField(NULL);
        }
        $this->em->flush();

        for ($i = 0; $i < count($order) - 1; $i++) {
            $field = $this->em->getRepository(PreRegistrationFormField::class)->findOneBy((['id' => $order[$i]]));
            $nextField = $this->em->getRepository(PreRegistrationFormField::class)->findOneBy((['id' => $order[$i + 1]]));
            $field->setNextFormField($nextField);
        }
        $this->em->flush();

        return new JsonResponse(null, 200);

    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    #[Route('candidate/preregistration', name: 'app_pre_registration_form')]
    public function preRegistrationForm(Request $request, UploaderHelper $uploaderHelper): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        $departments = $this->getUser()->getDepartments()->toArray();
        $start = $this->em->getRepository(PreRegistrationFormField::class)->findOneBy(['recruitmentSession' => $recruitmentSession, 'previousFormField' => NULL]);
        $fields = [];

        if ($start != null) {
            if ($recruitmentSession->getDepChoiceMaxNbre() > 0) {
                if (empty($start->getDepartments()->toArray()))
                    $fields[] = $start;
                else {
                    foreach ($departments as $department) {
                        if (in_array($department, $start->getDepartments()->toArray()))
                            $fields[] = $start;
                    }
                }
                while ($start->getNextFormField() != null) {
                    if (empty($start->getNextFormField()->getDepartments()->toArray()))
                        $fields[] = $start->getNextFormField();
                    else {
                        foreach ($departments as $dep) {
                            if (in_array($dep, $start->getNextFormField()->getDepartments()->toArray()))
                                $fields[] = $start->getNextFormField();
                        }
                    }
                    $start = $start->getNextFormField();
                }
            } else {
                $fields[] = $start;
                while ($start->getNextFormField() != null) {
                    $fields[] = $start->getNextFormField();
                    $start = $start->getNextFormField();
                }
            }
        }

        $form = $this->createForm(PreRegistrationFormType::class, [
            'fields' => $fields,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            foreach ($formData['fields'] as $field) {
                $Formfields[$field->getId()] = $field;
            }
            foreach ($formData as $id => $result) {
                if (is_numeric($id) && $result) {
                    $response = new \App\Entity\Response();
                    $response->setPreRegistrationFormField($Formfields[$id]);
                    if (is_array($result))
                        $response->setResponse(json_encode($result));
                    else{
                        if ($result instanceof DateTime) {
                            $result = $result->format('Y-m-d H:i:s');
                            $response->setResponse($result);
                        }elseif ($Formfields[$id]->getType() === 'file'){
                            /** @var UploadedFile $uploadedFile */
                            $uploadedFile = $result;
                            $newFilename = $uploaderHelper->uploadPreRegistrationFormResponse($uploadedFile);
                            $response->setResponse($newFilename);
                        }
                        else
                            $response->setResponse($result);
                    }
                    $this->getUser()->addResponse($response);
                    $this->em->persist($response);
                }
            }
            $this->em->flush();
            $this->addFlash('success', 'Your application has been successfully submitted.');
            return $this->redirectToRoute('app_candidate');
        }
        return $this->render('pre_registration/preregistration_form.html.twig', [
            'PreRegistrationForm' => $form->createView(),
            'recruitmentSession' => $recruitmentSession
        ]);
    }

    #[Route('admin/preregistration/result/data/{user}', name: 'app_pre_registration_single_response', methods: ['post'])]
    public function getResultData(User $user): Response
    {
        $responses = [];
        $temp = $user->getResponses();
        foreach ($temp as $response) {
            if (is_object(json_decode($response->getResponse())) || is_array(json_decode($response->getResponse()))) {
                $values = [];
                foreach (json_decode($response->getResponse()) as $id) {
                    $exist = false;
                    foreach ($response->getPreRegistrationFormField()->getPreRegistrationFormFieldOptions() as $option) {
                        if ($option->getId() == $id) {
                            $exist = true;
                            $values[] = $option->getValue();
                        }
                    }
                    if (!$exist) {
                        $values[] = $id;
                    }
                }
                $responses[$response->getPreRegistrationFormField()->getLabel()] = $values;
            }  else
                $responses[$response->getPreRegistrationFormField()->getLabel()] = $response;
        }
        if (!empty($responses)) {
            $status = 'hasData';
        } else {
            $status = 'dontHaveData';
        }
        $template = $this->render('pre_registration/result_data.html.twig', ['responses' => $responses, 'user' => $user, 'status' => $status]);
        return new JsonResponse(['template' => $template->getContent()]);
    }


    #[Route('admin/preregistration/result', name: 'app_pre_registration_result')]
    public function result(): Response
    {
        $recruitmentSession = $this->em->getRepository(RecruitmentSession::class)->findOneBy(['current' => true]);
        if ($recruitmentSession){
        $users =  $this->em->getRepository(User::class)->findByRoleAndSession('ROLE_CANDIDATE', $recruitmentSession);
        }else{
            $this->addFlash('error', 'there is no current recruitment session');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('pre_registration\result.html.twig', ['users' => $users]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('admin/preregistration/result/submit', name: 'app_pre_registration_result_submit', methods: ['post'])]
    public function postUserResult(Request $request,MailerInterface $mailer): Response
    {
        $data = $request->request->get("result");
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $request->request->get("user")]);
        if ($data == "accept") {
            if ($user->getResult() != null) {
                $user->getResult()->setPreRegistration(true);
            } else {
                $result = new Result;
                $result->setPreRegistration(true);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setPreRegistrationEmail(false);
            $this->em->flush();
        }
        elseif ($data == "refuse") {
            if ($user->getResult() != null) {
                $user->getResult()->setPreRegistration(false);
            } else {
                $result = new Result;
                $result->setPreRegistration(false);
                $this->em->persist($result);
                $user->setResult($result);
            }
            $user->getResult()->setPreRegistrationEmail(false);
            $this->em->flush();
        }
        elseif ($data == "mail"){
            if ($user->getResult() != null) {
                if ($user->getResult()->getPreRegistration() === true) {
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName().' '.$user->getLName()))
                        ->subject('[ Pre-Registration Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_accepted.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setPreRegistrationEmail(true);
                }
                elseif ($user->getResult()->getPreRegistration() === false){
                    $email = (new TemplatedEmail())
                        ->from(new Address('email@your-domain.com', 'JOIN'))
                        ->to(new Address($user->getEmail(), $user->getFName().' '.$user->getLName()))
                        ->subject('[ Pre-Registration Selection Result ]')
                        ->htmlTemplate('email/preregistration_selection_refused.html.twig')
                        ->context([
                            'user' => $user
                        ]);
                    $mailer->send($email);
                    $user->getResult()->setPreRegistrationEmail(true);
                }
                $this->em->flush();
            }
            else {
                $data = 'mail_not_sent';
            }
        }
        return new JsonResponse(['result' => $data]);
    }

    #[Route('/admin/preregistration/file/{file}', name: 'app_pre_registration_file_view')]
    public function downloadPreRegistrationFile($file, UploaderHelper $uploaderHelper): StreamedResponse
    {

        $response = new StreamedResponse(function () use ($file, $uploaderHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploaderHelper->readStream('pre_registration_form_results/' . $file, false);
            stream_copy_to_stream($fileStream, $outputStream);
        });
        $response->headers->set('content-Type', 'application/pdf');
        return $response;
    }

}
