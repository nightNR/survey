/**
 * Created by nightnr on 15.01.17.
 */
if(!window.Survey) {
    window.Survey = {
        init: function() {
            Survey.form.registerSubmitListener();
            Survey.flashMessages.registerListener();
            Survey.list.enableSort();
            Survey.admin.tools.init();
        },

        globalState: {
            'edited_survey': null,
            'edited_form': null
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
                                    $(this).remove();
                                });
                            }, 2000)
                        });

                    });
                });
                var config = {childList: true};

                observer.observe(messageContainer, config);
            }
        },

        admin: {
            list: {
                sort: function (event, element) {
                    try {
                        var count = 1;
                        var dataToCall = {
                            'items': []
                        };
                        $(element.item).parent().find("a").each(function(){
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
                for(var entry in data) {
                    if(data.hasOwnProperty(entry)) {
                        switch(entry) {
                            case 'flashMessage':
                                Survey.flashMessages.addFlashMessage(data[entry].status, data[entry].message);
                                break;
                            case 'form':
                                Survey.admin.tools.form.clear();
                                Survey.admin.tools.openForm(data[entry]);
                                break;
                            case 'closeModal':
                                Survey.admin.tools.form.closeModal(data[entry]);
                                break;
                            case 'reload':
                                Survey.admin.tools.reload(data[entry]);
                        }
                    }
                }
            },
            tools: {
                init: function () {
                    $('.tools').find('button').each(function () {
                        $(this).on('click', function (event) {
                            event.preventDefault();
                            Survey.admin.tools.runCommand(this);
                        })
                    });
                    $('#admin-modal').on('hidden.bs.modal', Survey.admin.tools.form.clear);
                },
                runCommand: function(element) {
                    switch($(element).data('action')) {
                        case 'add-survey':
                            $('#admin-modal').modal('show');
                            Survey.admin.makeApiCall('survey', 'createOrEditSurvey', {}, Survey.admin.handleResponse);
                            break;
                        case 'edit-survey':
                            Survey.globalState.edited_survey = $(element).data('id');
                            $('#admin-modal').modal('show');
                            Survey.admin.makeApiCall('survey', 'createOrEditSurvey', {'data': {
                                'survey_id': $(element).data('id')
                            }}, Survey.admin.handleResponse);
                            break;
                        case 'delete-survey':
                            Survey.admin.makeApiCall('survey', 'removeSurvey', {'data': {
                                'survey_id': $(element).data('id')
                            }}, Survey.admin.handleResponse);
                            break;
                        case 'add-form':
                            $('#admin-modal').modal('show');
                            Survey.admin.makeApiCall('form', 'createOrEditForm', {'data': {
                                'survey_id': $(element).data('survey_id')
                            }}, Survey.admin.handleResponse);
                            break;
                        case 'edit-form':
                            Survey.globalState.edited_form = $(element).data('id');
                            $('#admin-modal').modal('show');
                            Survey.admin.makeApiCall('form', 'createOrEditForm', {'data': {
                                'form_id': $(element).data('id')
                            }}, Survey.admin.handleResponse);
                            break;
                        case 'delete-form':
                            Survey.admin.makeApiCall('form', 'removeForm', {'data': {
                                'form_id': $(element).data('id')
                            }}, Survey.admin.handleResponse);
                            break;
                    }
                },
                openForm: function(data) {
                    var loading = $('#admin-modal').find('.loading');
                    loading.hide();
                    loading.parent().append(data);
                    loading.parent().find('form').submit(function(e){
                        e.preventDefault();
                        Survey.admin.tools.form.submitData(this);
                    });
                    console.log(data);
                },
                reload: function(timer) {
                    setTimeout(function(){
                        location.reload();
                    }, timer);
                },
                form: {
                    submitData: function(form) {
                        var formData = $(form).serializeArray();
                        var service = null;
                        var action = null;
                        switch($(form).attr('name')) {
                            case 'survey':
                                service = 'survey';
                                action = 'createOrEditSurvey';
                                break;
                            case 'form':
                                service = 'form';
                                action = 'createOrEditForm';
                                break;
                        }
                        Survey.admin.tools.form.makeApiCall(service, action, formData, Survey.admin.handleResponse)
                    },
                    makeApiCall: function(service, command, data, callback) {
                        dataObject = data;
                        dataObject.push({
                            'name': 'command',
                            'value': command
                        });
                        dataObject.push({
                            'name': 'service',
                            'value': service
                        });
                        dataObject.push({
                            'name': 'data[data][survey_id]',
                            'value': Survey.globalState.edited_survey
                        });
                        dataObject.push({
                            'name': 'data[data][form_id]',
                            'value': Survey.globalState.edited_form
                        });
                        console.log(data);
                        $.ajax({
                            url: apiUrl,
                            method: 'POST',
                            data: dataObject,
                            success: function(data) {
                                callback(data);
                            }
                        });
                    },
                    clear: function (e) {
                        $('#admin-modal').find('form').each(function () {
                            $(this).remove();
                        });
                        $('#admin-modal').find('h2').each(function () {
                            $(this).remove();
                        });
                        $('#admin-modal').find('.loading').show();
                    },
                    closeModal: function(timer) {
                        setTimeout(function(){
                            $('#admin-modal').modal('hide');
                        }, timer)
                    }
                }
            }
        }
    }
}

$(function(){
    Survey.init();
    $.material.init();
});