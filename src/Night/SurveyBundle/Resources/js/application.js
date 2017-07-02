/**
 * Created by nightnr on 15.01.17.
 */
if(!window.Survey) {
    window.Survey = {
        init: function() {
            Survey.form.registerSubmitListener();
            Survey.flashMessages.registerListener();
            Survey.list.enableSort();
        },

        form: {
            registerSubmitListener: function() {
                $("#submit-next").on('click', function(e){
                    e.preventDefault();
                    $("#" + FORM_ID).submit();
                })
            }
        },

        list: {
            enableSort: function() {
                $(".sortable-table").sortable({
                    tolerance: 'pointer',
                    revert: 'invalid',
                    placeholder: 'placeholder list-group-item',
                    forceHelperSize: true,
                    update: Survey.admin.list.sort
                });
            }
        },

        flashMessages: {
            addFlashMessage: function(status, message) {
                var messageElement = $(document.createElement('div')).addClass('alert');
                var messageContainer = $('#flash-messages');
                switch(status) {
                    case 'OK':
                        messageElement.addClass('alert-success');
                }
                messageElement.text(message);
                messageContainer.append(messageElement);
            },
            registerListener: function() {

                var messageContainer = document.getElementById('flash-messages');
                var observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        console.log(mutation);
                        mutation.addedNodes.forEach(function(message) {
                            setTimeout(function () {
                                $(message).fadeOut(200, function(e) {
                                    $(e).remove();
                                });
                            }, 2000)
                        });

                    });
                });
                var config = {childList: true};

                // pass in the target node, as well as the observer options
                observer.observe(messageContainer, config);
                // $('#flash-messages').on('change', function(e){
                //     console.log(e);
                //     $(this).find('.alert').each(function() {
                //         setTimeout(function(){
                //             $(this).remove();
                //         }, 2000)
                //     })
                // })
            }
        },

        admin: {
            list: {
                sort: function (event, element) {
                    try {
                        var count = 1;
                        // console.log(event);
                        // console.log($(element.item).parent());
                        // console.log($("a.list-group.button"));
                        var dataToCall = {
                            'items': []
                        };
                        $(element.item).parent().find("a").each(function(){
                            console.log();
                            $(this).attr("data-order", count);
                            $(this).find(".order").text(count);
                            dataToCall.items.push({
                                'id': $(this).data('id'),
                                'order': $(this).data('order')
                            });
                            count++;
                        });
                        console.log(dataToCall);
                        Survey.admin.makeApiCall('survey', 'reorderForms', dataToCall, Survey.admin.handleResponse);
                    } catch(e){
                        console.log(e);
                    }
                }
            },
            makeApiCall: function(service, command, data, callback) {
                $.ajax({
                    url: apiUrl,
                    method: 'POST',
                    data: {
                        service: service,
                        command: command,
                        data: data
                    },
                    success: function(data) {
                        callback(data);
                    }
                });
            },
            handleResponse: function(data) {
                Survey.flashMessages.addFlashMessage(data.status, data.message);
            }
        }
    }
}

$(function(){
    Survey.init();
    $.material.init();
});