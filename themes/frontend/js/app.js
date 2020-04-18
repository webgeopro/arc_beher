Array.prototype.clean = function(deleteValue) {
  for (var i = 0; i < this.length; i++) {
    if (this[i] == deleteValue) {         
      this.splice(i, 1);
      i--;
    }
  }
  return this;
};

window.Modals = {};
Modals = (function () {

    Modals.prototype.bindEvents = function () {
        var that = this;

        $('.js-modalClose').on('click', {modal: this}, this.hide);
        $('.js-modalOpen').on('click', {modal: this}, this.show);
        $('.overlay').on('click', {modal: this}, this.hide);

        $(document).on('keyup', function (e) {
            if (e.keyCode == that.escKey) {
                that.hide({data: {modal: that}});
            }
        });
    };

    Modals.prototype.cacheElements = function () {
        this.$allPopups = $('.modal');
        this.$overlay = $('.overlay');
    };

    Modals.prototype.show = function (e) {
        e.preventDefault();

        var that = e.data.modal,
            idModal = $(this).data('modal'),
            href = $(this).attr('href');

        if (idModal) {
            that.hideAll();

            that.$popupBlock = $('#' + idModal);
            that._show();
        }
    };

    Modals.prototype.forceShow = function (idModal, callback) {
        this.$popupBlock = $('#' + idModal);
        if (this.$popupBlock.length !== 0) {
            this._show();

            if (typeof callback !== 'undefined' && typeof callback == 'function') {
                callback();
            }
        }
    };

    Modals.prototype.hide = function (e) {
        var that = e.data.modal;
        if (typeof that.$popupBlock !== 'undefined') {
            that._hide();
        }
    };

    Modals.prototype.hideAll = function () {
        this.$overlay.hide();
        this.$allPopups.hide();
    };

    Modals.prototype.forceHide = function (idModal, callback) {
        this.$popupBlock = $('#' + idModal);
        if (this.$popupBlock.length !== 0) {
            this._hide();

            if (typeof callback !== 'undefined' && typeof callback == 'function') {
                callback();
            }
        } else {
            return false;
        }
    };

    Modals.prototype._show = function () {
        this.$overlay.show();
        this.$popupBlock.fadeIn(250);
    };

    Modals.prototype._hide = function () {
        var that = this;

        this.$popupBlock.fadeOut(250, function () {
            $('body').css('overflow', 'auto');
            that.$allPopups.hide();
        });
        this.$overlay.hide();
    };

    function Modals(popupId) {
        this.escKey = 27;
        this.cacheElements();
        this.bindEvents();
    }

    return Modals;

})();
window.Modals.instance = new Modals();

(function ($, root, undefined) {
	$(function () {
		'use strict';
		
		$(window).on('scroll', function (e) {
			// var scrollTop = $('.george').position().top;

			// if (scrollTop < -200) {
			if (document.body.scrollTop > 200) {
            $('.nav').addClass('white');
                 } else {
                    $('.nav').removeClass('white');
                 }
        });
		
		var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i) ? true : false;
    if (isMacLike === true) {
        $('html').addClass('mac');
    }

	  $('.nav__link').click(function (e) {
        //Сохраняем значение атрибута href в переменной:
        var target = $(this).attr('href');
        $('html, body').animate({scrollTop: $(target).offset().top}, 300);
        return false;
    });

    // Стоп видео при закрытии
    $('.js-modalClose').on('click', function () {
        var video = $(this).siblings('iframe');
        var videoURL = $(this).siblings('iframe').attr('src');

        video.attr("src", "");
        video.attr("src", videoURL);
    });

    // Изменение фона первых 2 экранов в мобильной версии
    var mobileBG = document.querySelector('.mobileBG');
    if(!!mobileBG){
        var step = Math.ceil(mobileBG.clientHeight / 100);

        $(window).on('scroll', function () {
            var scrollTop = document.body.scrollTop;
            if ((scrollTop < mobileBG.clientHeight) && (mobileBG.style.opacity <= 1)) {

                var value = Math.ceil(scrollTop / step) / 20;
                if(value <= 1)
                    mobileBG.style.opacity = value;
                // console.log(mobileBG.style.opacity);
            }
        });
    }

      $('.js-link').on('click', function () {
          var sectionName = $(this).attr('href');
          var sectionHeight = $(sectionName).offset().top - 65;
          $('body, html').animate({scrollTop: sectionHeight}, 650);
      });

      $('.js-link-jud').on('click', function () {
          var sectionName = $(this).attr('href');
          var sectionHeight = $(sectionName).offset().top - 84;
          $('body, html').animate({scrollTop: sectionHeight}, 650);
      });

      $('.btn--more').on('click', function () {
          var secHeight = $('.about').offset().top;
          $('body, html').animate({scrollTop: secHeight}, 550);
      });


      if ($('.about .swiper-container').length !== 0) {
          var swiper = new Swiper('.about .swiper-container', {
              pagination: '.about .swiper-pagination',
              loop: false,
              slidesPerView: 1,
              spaceBetween: 0,
              // onInit: function(){
              //     $('.swiper-slide').find('.winner').removeClass('animation');
              //     $('.swiper-slide-active').find('.winner').addClass('animation');
              // },
              // onSlideChangeEnd: function(swiper) {
              //     $('.js-activeSlideCounter').html(swiper.activeIndex + 1);
              //     $('.swiper-slide').find('.winner').removeClass('animation');
              //     $('.swiper-slide-active').find('.winner').addClass('animation');
              // },
              paginationBulletRender: function (index, className) {
                  if (index % this.slidesPerView == 0) {
                      return '<span class="' + className + '"></span>';
                  } else {
                      return '<span class="' + className + ' hidden"></span>';
                  }
              }
          });

          $(window).trigger('resize');
      }


        // Галерея победителей

        var i = new Image();
        i.src = '/themes/frontend/img/winners/BorisIvanov.jpg';
        i.onload = function () {
          $('.winnerImg_1').css('background', 'url(' + this.src + ') center top  no-repeat');

          $('.winnersPreloader').css('display', 'none');
          $('.winners .swiper-container').css('display', 'block');
          $('.winners .swiper-pagination').css('display', 'block');

          if ($('.swiper-container').length !== 0) {
              var swiper = new Swiper('.swiper-container', {
                  nextButton: '.winners .swiper-button-next',
                  prevButton: '.winners .swiper-button-prev',
                  pagination: '.winners .swiper-pagination',
                  loop: false,
                  slidesPerView: 1,
                  spaceBetween: 0,
                  onInit: function () {
                      $('.winners .swiper-slide').find('.winner').removeClass('animation');
                      $('.winners .swiper-slide-active').find('.winner').addClass('animation');
                  },
                  onSlideChangeEnd: function (swiper) {
                      $('.winners .js-activeSlideCounter').html(swiper.activeIndex + 1);
                      $('.winners .swiper-slide').find('.winner').removeClass('animation');
                      $('.winners .swiper-slide-active').find('.winner').addClass('animation');
                  },
                  paginationBulletRender: function (index, className) {
                      if (index % this.slidesPerView == 0) {
                          return '<span class="' + className + '"></span>';
                      } else {
                          return '<span class="' + className + ' hidden"></span>';
                      }
                  }
              });

              $(window).trigger('resize');
          }

        };
        $(window).trigger('resize');

        // Анимация label, кроме select
        $('.form__field, .form__textarea').focus(function () {
          $(this).siblings('.form__label').addClass('focus');
        });

        // Шаги в форме

        $('.mobileStartBtn').on('click', function (e) {
          $('.form__section-mobileStart').css('display', 'none');
          $('.form__section-barman').css('display', 'block');
        });

        // Валидация, маски, КЛАДР формы


        function fieldValidator(e, errorShow) {
            var isError = false;

            var value = $.trim($(e).val()),
                $parent = $(e).parent(),
                data = $(e).data(),
                isRequired = (data.required == true || $(e).attr('required') == true) ? true : false,
                isValid = false,
                minLength = (data.minlength || $(e).attr('minlength')),
                maxLength = (data.maxlength || $(e).attr('maxlength')),
                defaultValue = $(e).attr('placeholder') || '';

            if (!$($parent).hasClass('hidden')) {

                if (isRequired) {
                    if (value == '' || value == defaultValue) {
                        if(errorShow){
                            $($parent).addClass('invalid')
                                .find('.js-invalidMessage')
                                .html('Ты забыл указать ' + data.field + ' :(');
                        }
                        isError = true;
                    }
                }

                if (typeof data.validator !== 'undefined' && !(value == '' || value == defaultValue)) {
                    switch (data.validator) {
                        case 'number':
                            var regex = /\d+/;
                            if (!regex.test(value)) {
                                $($parent).addClass('invalid');
                                isError = true;
                            }
                            break;
                        case 'name':
                            var regex = /^[A-Za-zА-ЯЁа-яё ]+$/;
                            if (!regex.test(value)) {
                                $($parent);
                                isError = true;
                                if(errorShow) {
                                    $($parent).addClass('invalid')
                                        .find('.js-invalidMessage')
                                        .html('Используй только буквы и пробел');
                                }
                            }
                            break;
                        case 'email':
                            var lastAtPos = value.lastIndexOf('@'),
                                lastDotPos = value.lastIndexOf('.'),
                                isValid = (lastAtPos < lastDotPos && lastAtPos > 0 && value.indexOf('@@') == -1 && lastDotPos > 2 && (value.length - lastDotPos) > 2) ? true : false;

                            if (isValid === false) {
                                if(errorShow) {
                                    $($parent).addClass('invalid')
                                        .find('.js-invalidMessage')
                                        .html('Что-то не так с адресом почты');
                                }

                                isError = true;
                            }
                            break;
                    }
                }
            }

            return isError;
        }

        $('.js-btnValidate').on('click', function (e) {
            var $form = $(this).parent(),
            $validate = $($form).find('.js-validate'),
            isError = false;

            $($form).find('.invalid').removeClass('invalid');
            $($form).find('.js-invalidMessage').html('');

            $($form).find('.form__upload').each(function(){
                if (!$(this).hasClass('uploaded')) {
                    $(this).addClass('invalid');
                }
            });

            $($validate).each(function () {
                if($(this).hasClass('js-bundleBox')){
                    isError = true;
                    $(this).find('.js-bundle-validate').each(function () {
                        var validation = fieldValidator(this, false);
                        console.log(validation);
                        if(!validation){
                            isError = false;
                            return false;
                        }
                    });

                    if(isError){
                        console.log($(this));
                        var data = $(this).data();
                        $(this).addClass('invalid')
                            .find('.js-invalidMessage')
                            .html('Ты забыл указать ' + data.field + ' :(');
                    }
                }else{
                    isError = fieldValidator(this, true);
                }
            });

            if (isError) {
              e.preventDefault();
              return false;
            }
        });

        // $('.js-bundle-validate').on('click', function () {
        //     var form = $(this).parent(),
        //         boxes = form[0].querySelector('.js-bundleBoxField');
        //         console.log(boxes);
            // for(var i = 0; i < boxes.length; i++){
            //     var bundle = boxes[i].querySelectorAll('.js-bundle-validate');
            //     var key = true;
            //     for(var j = 0; j < bundle.length; j++){
            //         if(bundle[j].value === 0){
            //             return false;
            //         }
            //     }
            //
            //     var parent = bundle[0].parentNode.parentNode;
            //     if(!$(parent).hasClass('invalid')){
            //         parent.className = parent.className + ' invalid';
            //     }
            // }

            // $(boxes).each(function () {
            //     var field = $(this);
            //
            //     $(field).find('.js-bundle-validate').each(function (index, value) {
            //
            //         if($(this).val().length === 0){
            //
            //             if(!field.hasClass('invalid')){
            //                 console.log(field);
            //                 field.addClass('invalid');
            //             }
            //         }
            //     })
            // })


        // });


      $('.js-maskedMobilePhone').mask('+7 000 000 00 00')
      $('.js-maskedMobilePhone').on('focus blur', function (e) {
          if (e.type == 'focus') {
              $(this).attr('placeholder', '+7');
          } else {
              $(this).attr('placeholder', '');
          }
      });

      $('.js-maskedDate').mask('00/00/0000');
      $('.js-maskedDate').on('focus blur', function (e) {
          if (e.type == 'focus') {
              $(this).attr('placeholder', 'ДД/ММ/ГГГГ');
          } else {
              $(this).attr('placeholder', '');
          }
      });
      
      $('.js-kladr').on('focus, keyup', function(){
	      $('.autocomplete1').css('top', -($('.body-inner').offset().top) + $(this).offset().top + $(this).height());
	    });
			
			$('.js-kladr').kladr({
          withParents: 1,
          type: $.kladr.type.city,
          select: function (response) {
							
							// КЛАДР Города
              $('#js-town_id').val(response.id);
              $('#js-town_type').val(response.type);
              $('#js-town_name').val(response.name);
              
              // КЛАДР Региона
              var reg_name;
              
              if ( response.id == '9200000000000' ) {
	              // Севастополь
	              $('#region_name').val('Крым');
								$('#js-region_id').val('9100000000000');
								$('label[for="region_name"]')
              		.addClass('focus')
									.text('Республика');
								$('#js-region_name').val('Республика Крым');
              } else if ( response.id == '7700000000000' ) {
								// Москва
								$('#region_name').val('Московская');
								$('#js-region_id').val('5000000000000');
								$('label[for="region_name"]')
              		.addClass('focus')
									.text('Область');
								$('#js-region_name').val('Московская Область');
							} else if ( response.id == '7800000000000' ) {
								// Санкт-Петербург
								$('#region_name').val('Ленинградская');
								$('#js-region_id').val('4700000000000');	
								$('label[for="region_name"]')
              		.addClass('focus')
									.text('Область');
								$('#js-region_name').val('Ленинградская Область');
							} else {
								$('label[for="region_name"]')
              		.addClass('focus')
									.text(response.parents[0].type);
									
								$('#region_name').val(response.parents[0].name);
								$('#js-region_name').val( (response.parents[0].type == 'Область' ? response.parents[0].name + ' ' + response.parents[0].type : response.parents[0].type + ' ' + response.parents[0].name));
                $('#js-region_id').val(response.parents[0].id);
	            }
          }
      });

      $('.js-btnNext').on('click', function () {

          var parent = $(this).parents('.form__section');
          var stepItem = 'li:eq(' + parent.index() + 1 + ')';

          if ($(parent).find('.invalid').length == 0) {
              parent.fadeOut('fast');
              parent.next().fadeIn('fast');

              $('.pagination-form > li').removeClass('current');
              $('.pagination-form').find('li:eq(' + parent.index() + ')').next().addClass('current');

              var stepOffset = $('#anketForm').offset();
              if (typeof stepOffset.top !== 'undefined') {
                  window.scrollTo(0, stepOffset.top);
              }
          }
      });

      $('.js-btnSend').on('click', function() {
          if ($('#havePassport').prop('checked') == false ||
              $('#part').prop('checked') == false ||
              $('#profile').prop('checked') == false ||
              $('#data').prop('checked') == false
          ) {
              $('#js-termsValidation').text('Ты забыл подтвердить участие');
              return false;
          }

          // if (!$('.js-photofile')[0].files.length) {
          //     $('#js-termsValidation').text('Ты забыл добавить своё фото');
          //     return false;
          // }
          //
          // if (!$('.js-musephoto')[0].files.length) {
          //     $('#js-termsValidation').text('Ты забыл добавить картинку вдохновения');
          //     return false;
          // }
          //
          // if (!$('.js-cocktailphoto')[0].files.length) {
          //     $('#js-termsValidation').text('Ты забыл добавить фото коктейля');
          //     return false;
          // }

          $('#js-termsValidation').text('');

          var data = new FormData();
          var fields = $("#anketForm").serializeArray();

          fields.map(function(field) {
              data.append(field.name, field.value);
          });
          data.delete('town_id');
          data.delete('town_type');
          data.delete('town_name');
          data.delete('region_name');

          // TODO: remove it when we able to have alternative url photo links
          // data.delete('coctailPhoto_link');
          // data.delete('inspirePhoto_link');
          // data.delete('userPhoto_link');

          // data.append('photofile', $(".js-photofile").prop('files')[0]);
          // data.append('musephoto', $(".js-musephoto").prop('files')[0]);
          // data.append('cocktailphoto', $(".js-cocktailphoto").prop('files')[0]);

          data.append('ingred', data.getAll('ingreds').clean('').join(', '));
          data.delete('ingreds');

          data.append('decor', data.getAll('decors').clean('').join(', '));
          data.delete('decors');

          $('.js-btnSend').text('Отправка...');

          var settings = {
            "async": true,
            "url": "/profile/create/",
            "method": "POST",
            "headers": {
              "accept": "application/json",
              "cache-control": "no-cache"
            },
            "processData": false,
            "contentType": false,
            "mimeType": "multipart/form-data",
            "data": data
          };

          $.ajax(settings).done(function (response) {
              var answer = JSON.parse(response);
              var shareUrl = answer.shareUrl;
              var profileId = Object.keys(answer.success)[0];
              $('#js-profileShareLink').attr('href', shareUrl).data('id', profileId);

              $('.form__section-terms').fadeOut('fast');
              $('.form__section-success').fadeIn('fast');
          }).fail(function (err) {
              console.log(err);
          }).always(function() {
              $('.js-btnSend').text('Продолжить');
          });
      });

      $('.js-formUpload').on('click', function () {
          if (!$(this).hasClass('uploaded')) {
              $(this).addClass('hovered');
          }
      });

      $('.js-fileUpload').fileupload({
          url: '/profile/preview/',
          autoUpload: true,
          acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
          formData: {},
          dataType: 'json',
          beforeSend: function(xhrObj) {
		        xhrObj.setRequestHeader("Accept","");
		        xhrObj.setRequestHeader("Accept","application/json");
			    },
          add: function (e, data) {
              $(this).parent().addClass('load');
              data.submit();
          },
          progressall: function (e, data) {
              var dots = parseInt((data.loaded / data.total * 100) / 4, 10);
              $(this).parent().find('.loader li:lt(' + dots + ')').addClass('active');
          },
          done: function (e, data) {
	            var response = data.result;
	            console.log(response);
              if (!response.error) {
								console.log(response);
                  var $parent = $(this).parent();
                  $($parent).addClass('finish');
                  $($parent).find('.form__uploadResult').html('<img src="' + response.preview + '" alt="">');

                  $($parent).addClass('uploaded');
                  $($parent).removeClass('hovered');
              }
          }
      });

      $('.js-uploadOther').on('click', function () {
          $(this).parent().removeClass('load finish');
      });

      $('.js-savePhotoByLink').on('click', function () {
          var data = $(this).data(),
              url = $('#' + data.field).val(),
              $uploadBox = $(this).parent().parent();

          if ($.trim(url) !== '') {
              $($uploadBox).addClass('load');

              $.ajax({
                  url: '/profile/preview/',
                  type: 'post',
                  beforeSend: function(xhrObj) {
                      xhrObj.setRequestHeader("Accept","");
                      xhrObj.setRequestHeader("Accept","application/json");
                  },
                  // data: 'type=' + data.type + '&url=' + url,
                  data: data.type + '=' + url,
                  success: function (response) {
                      $($uploadBox).removeClass('load');

                      if (!response.error) {
                          console.log($uploadBox);
                          $($uploadBox).addClass('finish');
                          $($uploadBox).find('.form__uploadResult').html('<img src="' + response.preview + '" alt="">');

                          $($uploadBox).addClass('uploaded');
                      }
                  }
              });
          } else {
              $($uploadBox).addClass('invalid');
          }


      });

      // Auto resize textarea 

      autosize($('.form__textarea'));

      // Счетчик символов textarea

      var textLen = 500;
      $('.form__textarea').on('keyup paste', function () {
          var thisForm = $(this).parent();

          if ($(this).val().length > textLen) {
              $(this).val($(this).val().substr(0, textLen));
              thisForm.find($('.form__textareaLen')).addClass('form__textareaLen-invalid');
          }

          var remaining = parseInt(textLen - $(this).val().length, 10),
              $textareaLen = thisForm.find('.form__textareaLen');

          if (remaining <= 100) {
              $($textareaLen).removeClass('hidden');
              $($textareaLen).find('span').text(remaining);
          } else {
              $($textareaLen).addClass('hidden');
          }
      });


      // Добавление поля ингредиентов и украшений

      $('.form__btnAdd').on('click', function() {
          var fname = $(this).data('fieldname');
          $(this).before('<input class="form__field" type="text" name="' + fname + '">')
      });


      // Chosen и анимация его label

      $('.chosen-select').chosen({disable_search_threshold: 10, width: "100%"});
      $('.chosen-container').on('click', function () {
          $(this).siblings('.form__label-select').addClass('focus');
      });

      //отключение валидации при фокусе элемента в форме обратной связи

      $('.feedback .js-validate').on('focus', function () {
          $(this).parent().removeClass('error');
      });

      //очистка полей формы обратной связи перед открытием

      $('.feedbackOpen').on('click', function () {
          $('.feedback .js-validate').prop('disabled', false);
          $('.feedback .js-validate').val('');
          $('.feebackFormSubmite').css('display', 'inline-block');
          $('.feedback .ready').css('display', 'none');
      });

      //отключение валидации при фокусе элемента в форме обратной связи

      $('.feedback .js-validate').on('focus', function (e) {
          $(this).parent().removeClass('error');
      });

      $('.feedbackOpen').on('click', function () {
          $('.feedback .js-validate').prop('disabled', false);
          $('.feedback .js-validate').val('');
          $('.feebackFormSubmite').css('display', 'inline-block');
          $('.feedback .feedback-done').css('display', 'none');
          $('.feedback .feedback-sending').css('display', 'none');
      });

      // Отправка формы обратной связи

      $('.feebackFormSubmite').on('click', function () {
          $('.feebackFormSubmite').css('display', 'none');
          $('.feedback .feedback-sending').css('display', 'inline-block');

          var formData = new FormData();
          formData.append('title', document.querySelector('#title_FB').value);
          formData.append('email', document.querySelector('#email_FB').value);
          formData.append('phone', document.querySelector('#phone_FB').value);
          formData.append('message', document.querySelector('#message_FB').value);


          var xhr = new XMLHttpRequest();

          xhr.open("POST", '/feedback/create/', true);
          xhr.setRequestHeader('Accept', 'application/json');
          xhr.onreadystatechange = function () {
              if (xhr.readyState != 4) return;
              if (xhr.status != 200) {
                  // обработать ошибку
                  console.log("Error - " + xhr.status + ': ' + xhr.statusText); // пример вывода: 404: Not Found

                  $('.feedback .feedback-sending').css('display', 'none');
                  $('.feebackFormSubmite').css('display', 'inline-block');


                  var result = JSON.parse(xhr.responseText);
                  if (!!result.error) {
                      for (var i = 0; i < result.error.null.length; i++) {
                          $('#' + result.error.null[i].field + '_FB').parent().addClass('error');
                      }
                  }

              } else {
                  // вывести результат;
                  var result = JSON.parse(xhr.responseText);

                  console.log("Result - " + result.success);

                  if (!!result.success) {
                      $('.feedback .feedback-sending').css('display', 'none');
                      $('.feedback .feedback-done').css('display', 'block');
                      $('.feedback .js-validate').prop('disabled', 'disabled');
                  }
              }
          };

          xhr.send(formData);
          return null;
      });


      // Маски для age_gate

      $('#ageDay').mask('D0', {translation: {'D': {pattern: /[0-3]/}}});
      $('#ageDay').on('blur', function () {
          var e = document.querySelector('#ageDay');
          if(Number(e.value) > 31) {
              e.value = '';
          }
      });
      $('#ageMonth').mask('AB', {translation: {'A': {pattern: /[0-1]/}, 'B': {pattern: /[0-9]/}}});
      $('#ageMonth').on('blur', function () {
          var e = document.querySelector('#ageMonth');
          if(Number(e.value) > 12) {
              e.value = '';
          }
      });
      $('#ageYear').mask('AB00', {translation: {'A': {pattern: /[1-2]/}, 'B': {pattern: /[0,9]/}}});
      $('#ageYear').on('blur', function () {
          var e = document.querySelector('#ageYear');
          if(Number(e.value) > 2016) {
              e.value = '';
          }
      });

      $('#js-profileShareLink').on('click', function (e) {
          e.preventDefault();
          var data = new FormData();
          data.append("_id", $(this).data('id'));
          data.append("shared", "true");

          $.ajax({
              "async": true,
              "url": "/profile/update/",
              "method": "POST",
              "headers": {
                  "accept": "application/json",
                  "cache-control": "no-cache"
              },
              "processData": false,
              "contentType": false,
              "mimeType": "multipart/form-data",
              "data": data
          });

          var url = $(this).attr('href');

          var left = (screen.width / 2) - parseInt(600 / 2, 10),
              top = (screen.height / 2) - parseInt(400 / 2, 10);

          var windowCoords = ['width=600',
                  'height=400',
                  'left=' + left,
                  'top=' + top],
              windowParams = ['toolbar=no', 'location=no', 'directories=no', 'status=no', 'menubar=no', 'scrollbars=no', 'resizable=no', 'copyhistory=no']


          windowParams = windowParams.concat(windowCoords);
          window.open(url, '', windowParams.join(', '));

      });

      if ($('.js-mapSVG').length !== 0) {
          var w = parseInt($('#map .hCenter').width() * 0.95, 10),
              h = parseInt(w * 679.954 / 1240.354, 10);

          $('.js-mapSVG').css({
              width: w,
              height: h
          });

          $(window).on('resize', function () {
              var w = parseInt($('#map .hCenter').width() * 0.95, 10),
                  h = parseInt(w * 679.954 / 1240.354, 10);

              $('.js-mapSVG').css({
                  width: w,
                  height: h
              });
          });
      }

      $('.prefooter__link-right').on('touchstart', function () {
          $(this).addClass('tapped');
      });


      // Проверка возраста
      //
      // $('.form-age').on('submit', function (e) {
      //     e.preventDefault();
      //
      //     var ageDay = $('#ageDay').val();
      //     var ageMonth = $('#ageMonth').val();
      //     var ageYear = $('#ageYear').val() || 2016;
      //
      //     if (checkAge(ageDay, ageMonth, ageYear)) {
      //         // window.location = '/';
      //         console.log('validation - ok');
      //         return true;
      //     } else {
      //         // $('.form-age').fadeOut('fast');
      //         // $('.age__rejection').fadeIn('fast');
      //         console.log('validation - error');
      //         return false;
      //     }
      //
      // });
      var formAge = document.querySelector('.form-age');
      if(!!formAge) {
          document.querySelector('.form-age').onsubmit = function () {
              var ageDay = $('#ageDay').val();
              var ageMonth = $('#ageMonth').val();
              var ageYear = $('#ageYear').val() || 2016;

              if (checkAge(ageDay, ageMonth, ageYear)) {
                  // window.location = '/';
                  console.log('validation - ok');
                  return true;
              } else {
                  // $('.form-age').fadeOut('fast');
                  // $('.age__rejection').fadeIn('fast');
                  console.log('validation - error');
                  return false;
              }
          };
      }

      function checkAge(day, month, year) {
          var current = new Date(),
              currentYear = current.getFullYear(),
              currentMonth = current.getMonth() + 1,
              currentDay = current.getDate();

          if ((currentYear - year) > 18) {
              return true;
          } else if ((currentYear - year) < 18) {
              return false;
          } else if (((currentYear - year) == 18) && ((currentMonth - month) > 0)) {
              return true;
          } else if (((currentMonth - month) == 0) && ((currentDay - day) >= 0)) {
              return true;
          } else {
              return false;
          }
      }


  });

  // счётчик символов в textarea feedback form
  var maxCount = 200;

  $("#counter").html(maxCount);

  $("#message_FB").keyup(function () {
      var revText = this.value.length;

      if (this.value.length > maxCount) {
          this.value = this.value.substr(0, maxCount);
      }
      var cnt = (maxCount - revText);
      if (cnt <= 0) {
          $("#counter").html('0');
      }
      else {
          $("#counter").html(cnt);
      }

  });

  // отцентровка изображений в личном кабинете
  var winnerImgBoxPA = document.querySelector('.personal-img');
  var winnerImgPA = document.querySelector('.personal-img img');
  var winnerBtnPA = document.querySelector('.personal-img button');

  if(!!winnerImgBoxPA && !!winnerImgPA){
      winnerImgBoxPA.scrollLeft = Math.ceil((winnerImgPA.clientWidth - winnerImgBoxPA.clientWidth) / 2);
      if(!!winnerBtnPA){
          winnerBtnPA.style.left = Math.ceil(winnerImgPA.clientWidth / 2) + "px";
      }
  }
		
})(jQuery, this);
		
// SVG MAP
(function ($, root, undefined) {
	$(function () {
		'use strict';
		
		var regions = 
		[ 
			{ id: '0100000000000', code: 'ad' },
			{ id: '0200000000000', code: 'bs' },
			{ id: '0300000000000', code: 'br' },
			{ id: '0400000000000', code: 'lt' },
			{ id: '0500000000000', code: 'da' },
			{ id: '0600000000000', code: 'in' },
			{ id: '0700000000000', code: 'kb' },
			{ id: '0800000000000', code: 'kk' },
			{ id: '0900000000000', code: 'kc' },
			{ id: '1000000000000', code: 'kl' },
			{ id: '1100000000000', code: 'ko' },
			{ id: '1200000000000', code: 'ml' },
			{ id: '1300000000000', code: 'mr' },
			{ id: '1400000000000', code: 'sa' },
			{ id: '1500000000000', code: 'so' },
			{ id: '1600000000000', code: 'ta' },
			{ id: '1700000000000', code: 'tv' },
			{ id: '1800000000000', code: 'ud' },
			{ id: '1900000000000', code: 'hk' },
			{ id: '2100000000000', code: 'cu' },
			{ id: '2200000000000', code: 'al' },
			{ id: '2300000000000', code: 'ks' },
			{ id: '2400000000000', code: 'kr' },
			{ id: '2500000000000', code: 'pr' },
			{ id: '2600000000000', code: 'st' },
			{ id: '2700000000000', code: 'ha' },
			{ id: '2800000000000', code: 'am' },
			{ id: '2900000000000', code: 'ar' },
			{ id: '3000000000000', code: 'as' },
			{ id: '3100000000000', code: 'bl' },
			{ id: '3200000000000', code: 'bn' },
			{ id: '3300000000000', code: 'vm' },
			{ id: '3400000000000', code: 'vl' },
			{ id: '3500000000000', code: 'vo' },
			{ id: '3600000000000', code: 'vn' },
			{ id: '3700000000000', code: 'iv' },
			{ id: '3800000000000', code: 'ir' },
			{ id: '3900000000000', code: 'kn' },
			{ id: '4000000000000', code: 'kj' },
			{ id: '4100000000000', code: 'ka' },
			{ id: '4200000000000', code: 'km' },
			{ id: '4300000000000', code: 'ki' },
			{ id: '4400000000000', code: 'kt' },
			{ id: '4500000000000', code: 'ku' },
			{ id: '4600000000000', code: 'ky' },
			{ id: '4700000000000', code: 'le' },
			{ id: '4800000000000', code: 'lp' },
			{ id: '4900000000000', code: 'ma' },
			{ id: '5000000000000', code: 'mc' },
			{ id: '5100000000000', code: 'mu' },
			{ id: '5200000000000', code: 'nn' },
			{ id: '5300000000000', code: 'no' },
			{ id: '5400000000000', code: 'nv' },
			{ id: '5500000000000', code: 'om' },
			{ id: '5600000000000', code: 'ob' },
			{ id: '5700000000000', code: 'or' },
			{ id: '5800000000000', code: 'pz' },
			{ id: '5900000000000', code: 'pe' },
			{ id: '6000000000000', code: 'ps' },
			{ id: '6100000000000', code: 'ro' },
			{ id: '6200000000000', code: 'rz' },
			{ id: '6300000000000', code: 'ss' },
			{ id: '6400000000000', code: 'sr' },
			{ id: '6500000000000', code: 'sh' },
			{ id: '6600000000000', code: 'sv' },
			{ id: '6700000000000', code: 'sm' },
			{ id: '6800000000000', code: 'tb' },
			{ id: '6900000000000', code: 'tr' },
			{ id: '7000000000000', code: 'tm' },
			{ id: '7100000000000', code: 'tl' },
			{ id: '7200000000000', code: 'tu' },
			{ id: '7300000000000', code: 'ul' },
			{ id: '7400000000000', code: 'cl' },
			{ id: '7500000000000', code: 'zb' },
			{ id: '7600000000000', code: 'yr' },
			{ id: '7900000000000', code: 'eu' },
			{ id: '8000000000000', code: '' },
			{ id: '8100000000000', code: '' },
			{ id: '8200000000000', code: '' },
			{ id: '8300000000000', code: 'ne' },
			{ id: '8400000000000', code: '' },
			{ id: '8500000000000', code: '' },
			{ id: '8600000000000', code: 'ht' },
			{ id: '8700000000000', code: 'ch' },
			{ id: '8800000000000', code: '' },
			{ id: '8900000000000', code: 'ya' },
			{ id: '2000000000000', code: 'cc' }
		];
		
		var pin_sizes = [22, 32, 44, 58, 88, 128];
		
		$.ajax({
			dataType: "json",
			url: '/answer.json',
			 beforeSend: function(xhrObj){
          xhrObj.setRequestHeader("Content-Type","application/json");
          xhrObj.setRequestHeader("Accept","application/json");
       },
       type: "GET",
       url: '/profile/stats/',
       // url: '/ans.json',
			success: function(data) {
				ajaxCallback(data, regions);
			}
		});
		
		function ajaxCallback(mapData, regions) {
			var colors = {};
			var pins = {};
			var colors_list = ['#d2a180', '#c39271', '#af7e5d', '#b88766', '#e8b796'];
			var map_popup = $('.map-popup');
			
			$('#js__map-count').text(mapData.total);

      function declOfNum(number, titles) {  
        var cases = [2, 0, 1, 1, 1, 2];  
        return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];  
      }

      var ankets = ['отправленная анкета', 'отправленные анкеты', 'отправленных анкет'];
      $('#js__map-words').text(declOfNum(parseInt(mapData.total), ankets));

			function showMapPopup(region_data, region_title) {
				var map_popup__title = map_popup.find('.map-popup__title');
				var map_popup__counter = map_popup.find('.map-popup__counter');
				var map_popup__body = map_popup.find('.map-popup__body');
				
				map_popup__title.text(region_title);
				map_popup__counter.text(region_data.count);
				
				map_popup__body.prepend('<div class="inner">');
				region_data.users.forEach(function(el) {
					map_popup__body.find('.inner').append(''
					+'<div class="map_popup__user" data-user-id="'+el._id.$id+'">'
						+'<div class="map_popup__user-name">'+ el.surname + ' ' + el.name + '</div>'
						+'<div class="map_popup__user-bar">бар &laquo;'+ el.bar +'&raquo;, '+ el.city +'</div>'
					+'</div>');
				});
				map_popup__body.find('.inner').perfectScrollbar({
					suppressScrollX: true,
					minScrollbarLength: 16,
					maxScrollbarLength: 16
				});
				
				$('.map-popup__overlay').show();
				map_popup.fadeIn(100);
				
				$(document).on('keyup', '.map-popup__footer input', function(){
					var q_name = $(this).val();
					if ( q_name === '' ) {
						map_popup__body.find('.map_popup__user').show();
					} else {
						map_popup__body.find('.map_popup__user').filter(function( index, el ) {
					    return ($(el).find('.map_popup__user-name').text().indexOf(q_name) < 0);
					  }).hide();
					  map_popup__body.find('.map_popup__user').filter(function( index, el ) {
					    return ($(el).find('.map_popup__user-name').text().indexOf(q_name) > -1);
					  }).show();
					}
				  map_popup__body.find('.inner').perfectScrollbar('update');
				});
				
				$(document).on('click', '.map-popup__close, .map-popup__overlay', function(){
					$('.map-popup__overlay').hide();
					map_popup.fadeOut(100, function(){
						map_popup__title.text('');
						map_popup__counter.text('');
						map_popup__body.html('');
						$('.map-popup__footer input').val('');
					});
				});
				
				$(document).on('click', '.map_popup__user', function(){
					var id = $(this).data('user-id');
					window.open('/p/' + id + '/', '_blank');
				});
			}
			
			regions.forEach(function(el) {
				if (mapData.hasOwnProperty(el.id)) {
					var pinCounter = mapData[el.id].count;
					
					if ( pinCounter < 10 ) {
						var scaleSize = 22;
						var fontStyle = 'font-size: 0px;'
					} else if ( pinCounter >= 10 && pinCounter < 20 ) {
						var scaleSize = 32;
						var fontStyle = 'font-size: 16px;'
					} else if ( pinCounter >= 20 && pinCounter < 40 ) {
						var scaleSize = 44;
						var fontStyle = 'font-size: 18px;'
					} else if ( pinCounter >= 40 && pinCounter < 50 ) {
						var scaleSize = 58;
						var fontStyle = 'font-size: 20px;'
					} else if ( pinCounter >= 50 && pinCounter < 100 ) {
						var scaleSize = 88;
						var fontStyle = 'font-size: 30px;'
					} else { 
						var scaleSize = 128;
						var fontStyle = 'font-size: 39px;'
					}
					var lineHeight = scaleSize * 1.05;
					var pinStyle = 'style="width: ' + scaleSize + 'px; height: ' + scaleSize + 'px; line-height: ' + lineHeight + 'px; ' + fontStyle + '"';
					//var pinStyle = '';
					var pin = '<div class="map__pin" ' + pinStyle + '>' + pinCounter + '</div>';
					pins[el.code] = pin;
					colors[el.code] = colors_list[Math.floor(Math.random()*colors_list.length)];
				} else {
					colors[el.code] = '#ccc'; //colors_list[Math.floor(Math.random()*colors_list.length)];
				}
				
				
			});
			
			$(document).on('mouseleave', '#vmap path', function(e){
				var path_id = $(this).attr('id');
				if ( $(e.toElement).is('.map__pin') || $(e.toElement).is('.jqvmap-pin') ) {
					return;
				} else {
					$('#'+ path_id + '_pin')
				    	.fadeOut(200);
				}
			});
			
			$(document).on('mouseleave', '.jqvmap-pin', function(e){
				var code = $(this).attr('for');
				if ( $(e.toElement).is('#jqvmap1_' + code) ) {
					
				} else {
					$('#jqvmap1_' + code + '_pin')
				    	.fadeOut(200);
				}
			});
			
			$(document).on('click', '.jqvmap-pin', function(e){
				var region_code = $(this).attr('for');
				var region_id;
				regions.forEach(function(el) {
					if (el.code === region_code) {
						region_id = el.id;
						
						if ( mapData[region_id] ) {
							showMapPopup(mapData[region_id], mapData[region_id].region);
						}
						return;
					}
				});
			});
			
			$(document).on('click', '#vmap path', function(e){
				var region_code = $(this).attr('id').split("_").pop();
				var region_id;
				regions.forEach(function(el) {
					if (el.code === region_code) {
						region_id = el.id;
						
						if ( mapData[region_id] ) {
							showMapPopup(mapData[region_id], mapData[region_id].region);
						}
						return;
					}
				});
			});
			
			jQuery('#vmap').vectorMap(
			{
			    map: 'russia_en',
			    backgroundColor: 'transparent',
			    borderColor: '#818181',
			    borderOpacity: 1,
			    borderWidth: 1,
			    color: '#d2a180',
			    enableZoom: true,
			    hoverColor: '#664834',
			    hoverOpacity: null,
			    normalizeFunction: 'linear',
			    colors: colors,
			    selectedColor: null,
			    selectedRegions: null,
			    showTooltip: true,
			    pins: pins,
					pinMode: 'content',
			    onRegionClick: function(element, code, region)
			    {
			    
			    },
			    onRegionOver: function(event, code, region) {
				    $('#jqvmap1_' + code + '_pin')
				    	.fadeIn(200);
			    },
			    onRegionOut: function(event, code, region) {
				    
			    },
			    onLoad: function(event, map)
			    {
				    
			    }
			});
		
		}
		
				
		
	});
})(jQuery, this);
