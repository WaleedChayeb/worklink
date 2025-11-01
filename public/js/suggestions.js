/**
 * Ai Suggestions (helper) component
 */
"use strict";
/* global app, updateButtonState, launchToast, trans, JobCreate */

var AiSuggestions = {

    targetedClass: null,
    suggestionType: null,

    initAISuggestions: function(selector, type){
        this.setTargetedClass(selector);
        this.setSuggestionType(type);
        AiSuggestions.setDefaultDescription(type);
    },

    /**
     * Shows up the ai suggestion dialog
     */
    suggestDescriptionDialog: function(selector, type){
        this.initAISuggestions(selector, type);
        $('#suggest-description-dialog').modal('show');
    },

    /**
     * Set targeted html class to update the text to when saving a suggestion
     * @param className
     */
    setTargetedClass(className) {
        this.targetedClass = className;
    },

    /**
     * Sets suggestion type
     * @param type
     */
    setSuggestionType(type) {
        this.suggestionType = type;
    },

    /**
     * Saves the post description suggestion
     */
    saveSuggestion: function(){
        let description = $('#ai-request').val();
        description = description.replaceAll("\n", "<br>");
        description = description.replaceAll(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        let validDescription = this.validateDescription();
        if(validDescription) {
            $('#ai-request').removeClass('is-invalid');
            $('#suggest-description-dialog').modal('hide');
            $('trix-editor[input="'+this.targetedClass.replace('#','')+'"]').html(description);
            $('#ai-request').removeClass('is-invalid');
        }
    },
    /**
     * Clears up post description suggestion
     */
    clearSuggestion: function(){
        $('#ai-request').val('');
    },

    /**
     * Generates a suggestion
     */
    suggestDescription: function () {
        let validDescription = this.validateDescription();
        if(validDescription) {
            $('#ai-request').removeClass('is-invalid');
            updateButtonState('loading',$('.suggest-description'), trans('Suggest'), 'light');
            let route = app.baseUrl + '/suggestions/generate';
            const AIQuery = $('#ai-request').val() + trans('Do not include any explanation. Do not add any company data in a separate category. Add emojis if consider so. The content will be feed to a trix js editor.');
            let data = {
                'text': AIQuery,
            };
            $.ajax({
                type: 'POST',
                data: data,
                url: route,
                success: function (response) {
                    if(response.message) {
                        $('#ai-request').val(response.message);
                    }
                    updateButtonState('loaded',$('.suggest-description'), trans('Suggest'));
                },
                error: function (result) {
                    if(result.status === 422 || result.status === 500 || result.status === 429) {
                        launchToast('danger',trans('Error'),result.responseJSON.message);
                    }
                    else if(result.status === 403){
                        launchToast('danger',trans('Error'),trans('Something went wrong, please try again'));
                    }
                    updateButtonState('loaded',$('.suggest-description'), trans('Suggest'));
                }
            });
        }
    },

    /**
     * Validate description length before saving / making another suggestion call
     * @returns {boolean}
     */
    validateDescription: function () {
        let description = $('#ai-request').val();
        if(description.length < 5){
            $('#ai-request').addClass('is-invalid');
            return false;
        }
        return true;
    },

    /**
     * sets default description
     */
    setDefaultDescription: function (type) {
        let description;
        if(type === 'jobDescription') {
            description = trans('Job post AI Description template',{
                'jobTitle':$('#title').val(),
                'siteName':app.siteName,
                'jobCategory': $('#category_id').find('option:selected').val(),
                'skills': JobCreate.state.skillsData.length > 0 ? JobCreate.state.skillsData.join(', ') : '',
                'area': $('#location').val()
            });
        }
        if(type === 'companyDescription') {
            description = trans('Company AI Description template', {
                'companyName':$('#company_name').val(),
                'jobCategory': $('#category_id').find('option:selected').val(),
                'skills': JobCreate.state.skillsData.length > 0 ? JobCreate.state.skillsData.join(', ') : '',
                'area': $('#company_hq').val()
            });
        }
        if(description) {
            $('#ai-request').val(description);
        }
    }
};
