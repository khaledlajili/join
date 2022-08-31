import $ from 'jquery'
function candidateSelection(Submiturl, singleUrl=false, individual = false) {
  var resultModal = document.getElementById('result-modal')
  $('.loading').css('visibility', 'visible')
  if(singleUrl){
    resultModal.addEventListener('shown.bs.modal', function (event) {
      $('.kanban-items-container').scrollTop(0)
      var triggerElement = $(event.relatedTarget)
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: singleUrl + triggerElement[0].id,
        success: function (data) {
          $('.loading').css('visibility', 'hidden')
  
          $('#result_body').html(data['template'])
        },
        error: function (data) {
          Alert(
            'Il ya une Probléme dans le serveur (réfresher la page) !!',
            'danger',
          )
        },
      }).then(function (data) {
        if (individual === true) {
          var selectMenuRow = $('.dep-choice')
          var selectMenuPopup = $('.dep-choice-popup')
          selectMenuPopup[0].value = selectMenuRow[0].value
          if (selectMenuRow) {
            selectMenuPopup.on('change', (e) => {
              const value = e.currentTarget.value
              selectMenuRow[0].value = value
            })
          }
        }
  
        $('.result_popup')
          .find('button')
          .click(function (e) {
            e.preventDefault()
            var current = $(e.currentTarget)
            var old = current.html()
            var others = $()
            var similar = $()
            $('.result')
              .children()
              .each(function (index) {
                if ($(this).html() != current.html()) {
                  others = others.add($(this))
                } else if ($(this).html() == current.html()) {
                  similar = similar.add($(this))
                }
              })
            $('.result_popup')
              .children()
              .each(function (index) {
                if ($(this).html() != current.html()) {
                  others = others.add($(this))
                } else if ($(this).html() == current.html()) {
                  similar = similar.add($(this))
                }
              })
  
            current.html(
              '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>',
            )
            var ajaxDataPopup = {
              user: current.data('user-id'),
              result: current.data('value'),
            }
            if (individual === true) {
              ajaxDataPopup['dep'] = $(
                '.selected-dep-' + current.data('user-id'),
              )[0].value
            }
            $.ajax({
              type: 'POST', //HTTP POST Method
              url: Submiturl, // Controller/View
              data: ajaxDataPopup,
              success: function (data) {
                $('.kanban-items-container').scrollTop(0)
                if (data['result'] == 'accept') {
                  $('.status').each(function (index) {
                    if ($(this).attr('id') == current.data('user-id')) {
                      $(this)
                        .removeClass('badge-soft-info badge-soft-danger')
                        .addClass('badge-soft-success')
                      $(this).html(
                        'Accepted<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>',
                      )
                    }
                  })
                  $('#status_popup')
                    .removeClass('badge-soft-info badge-soft-danger')
                    .addClass('badge-soft-success')
                  $('#status_popup').html(
                    'Accepted<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>',
                  )
                  current.html(old)
                  similar.each(function (index) {
                    if ($(this).data('user-id') == current.data('user-id'))
                      $(this).addClass('disabled')
                  })
                  others.each(function (index) {
                    if ($(this).data('user-id') == current.data('user-id'))
                      $(this).removeClass('disabled')
                  })
                  $('.email-status').each(function (index) {
                    if ($(this).attr('id') == current.data('user-id')) {
                        $(this)
                            .removeClass('badge-soft-success')
                            .addClass('badge-soft-danger')
                        $(this).html(
                            'Email not sent<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                        )
                    }
                })
                } else if (data['result'] == 'refuse') {
                  $('.status').each(function (index) {
                    if ($(this).attr('id') == current.data('user-id')) {
                      $(this)
                        .removeClass('badge-soft-info badge-soft-success ')
                        .addClass('badge-soft-danger')
                      $(this).html(
                        'Refused<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                      )
                    }
                  })
                  $('#status_popup')
                    .removeClass('badge-soft-info badge-soft-success ')
                    .addClass('badge-soft-danger')
                  $('#status_popup').html(
                    'Refused<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                  )
                  current.html(old)
                  //current.addClass("disabled");
                  similar.each(function (index) {
                    if ($(this).data('user-id') == current.data('user-id'))
                      $(this).addClass('disabled')
                  })
                  others.each(function (index) {
                    if ($(this).data('user-id') == current.data('user-id'))
                      $(this).removeClass('disabled')
                  })
                  $('.email-status').each(function (index) {
                    if ($(this).attr('id') == current.data('user-id')) {
                        $(this)
                            .removeClass('badge-soft-success')
                            .addClass('badge-soft-danger')
                        $(this).html(
                            'Email not sent<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                        )
                    }
                })
                }
              },
            })
          })
      })
    })
  }
  //get result
  $('.result')
    .find('button')
    .click(function (e) {
      e.preventDefault()
      var current = $(e.currentTarget)
      var old = current.html()
      var other
      var others = $()
      var similar = $()
      $('.result')
        .children()
        .each(function (index) {
          if ($(this).html() != current.html()) {
            others = others.add($(this))
          } else if ($(this).html() == current.html()) {
            similar = similar.add($(this))
          }
        })
      $('.result')
        .children()
        .each(function (index) {
          if ($(this).html() !== current.html()) {
            other = $(this)
          }
        })
      current.html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>',
      )
      var ajaxDataRow = {
        //Passing data
        user: current.data('user-id'),
        result: current.data('value'),
      }
      if (individual === true) {
        ajaxDataRow['dep'] = $(
          '.selected-dep-row-' + current.data('user-id'),
        )[0].value
      }
      $.ajax({
        type: 'POST', //HTTP POST Method
        url: Submiturl, // Controller/View
        data: ajaxDataRow,
        success: function (data) {
          if (data['result'] == 'accept') {
            $('.status').each(function (index) {
              if ($(this).attr('id') == current.data('user-id')) {
                $(this)
                  .removeClass('badge-soft-info badge-soft-danger')
                  .addClass('badge-soft-success')
                $(this).html(
                  'Accepted<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>',
                )
              }
            })
            current.html(old)
            similar.each(function (index) {
              if ($(this).data('user-id') == current.data('user-id'))
                $(this).addClass('disabled')
            })
            others.each(function (index) {
              if ($(this).data('user-id') == current.data('user-id'))
                $(this).removeClass('disabled')
            })
              $('.email-status').each(function (index) {
                  if ($(this).attr('id') == current.data('user-id')) {
                      $(this)
                          .removeClass('badge-soft-success')
                          .addClass('badge-soft-danger')
                      $(this).html(
                          'Email not sent<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                      )
                  }
              })
          } else if (data['result'] == 'refuse') {
            $('.status').each(function (index) {
              if ($(this).attr('id') == current.data('user-id')) {
                $(this)
                  .removeClass('badge-soft-info badge-soft-success ')
                  .addClass('badge-soft-danger')
                $(this).html(
                  'Refused<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                )
              }
            })
            current.html(old)
            similar.each(function (index) {
              if ($(this).data('user-id') == current.data('user-id'))
                $(this).addClass('disabled')
            })
            others.each(function (index) {
              if ($(this).data('user-id') == current.data('user-id'))
                $(this).removeClass('disabled')
            })
              $('.email-status').each(function (index) {
                  if ($(this).attr('id') == current.data('user-id')) {
                      $(this)
                          .removeClass('badge-soft-success')
                          .addClass('badge-soft-danger')
                      $(this).html(
                          'Email not sent<span class="ms-1 fas fa-close" data-fa-transform="shrink-2"></span>',
                      )
                  }
              })
          }
          else if (data['result'] == 'mail'){
              $('.email-status').each(function (index) {
                  if ($(this).attr('id') == current.data('user-id')) {
                      $(this)
                          .removeClass('badge-soft-danger')
                          .addClass('badge-soft-success')
                      $(this).html(
                          'Email sent<span class="ms-1 fas fa-check" data-fa-transform="shrink-2"></span>',
                      )
                      current.html(old)
                  }
              })
          }
        },
      })
    })
}
export default candidateSelection
