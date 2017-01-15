/**
 * Created by nightnr on 15.01.17.
 */
if(!window.Survey) {
    window.Survey = {
        init: function() {
            console.log(SURVEY_ID);
            Survey.form.registerSubmitListener();
        },

        form: {
            registerSubmitListener: function() {
                $("#submit-next").on('click', function(e){
                    e.preventDefault();
                    $("#" + FORM_ID).submit();
                })
            }
        }
    }
}

$(function(){
    Survey.init();
});