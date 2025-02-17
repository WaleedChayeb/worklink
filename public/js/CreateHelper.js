/**
 * CreateHelper class
 */
/* global JobCreate, redirect, companyData, jobData, trans, FileUpload, launchToast, app, user, Company */

var CreateHelper = {

    /**
     * Inits job create save event
     * @param type
     */
    initFormSave: function(type = 'save'){
        $('#job-data-form').on('submit',function (e) {
            CreateHelper.clearFormErrors();
            e.preventDefault();
            CreateHelper.saveFormData(type);
        });
    },

    /**
     * Creates the job listing
     * @param type
     */
    saveFormData: function (type) {
        // Filtering form data
        let filteredData = CreateHelper.normalizeFormData();

        if(type === 'create'){
            CreateHelper.saveDraftData();
        }
        else if(type === 'edit'){
            if(typeof companyData !== "undefined"){
                filteredData.push({name:'id',value:companyData.id});
            }
            if(typeof jobData !== "undefined"){
                filteredData.push({name:'id',value:jobData.id});
            }
        }

        $.ajax({
            type: 'POST',
            data: filteredData,
            url: $('#job-data-form').attr('action'),
            success: function (result) {
                JobCreate.isSavingRedirect = true;
                if(JobCreate.debugJobCreation){
                    if (confirm("[DEBUG] Continue? \r\n* Ok = Continue with redirect \r\n* Cancel = Stay on page, but the call is made.") === true) {
                        redirect(result.redirect);
                    }
                }
                else{
                    redirect(result.redirect);
                }
            },
            error: function (result) {
                launchToast('danger',trans('Error'),trans('Job create failed - please check errors.'));
                $.each(result.responseJSON.errors,function (field,error) {
                    let fieldElement = $('input[name="'+field+'"]');
                    if(fieldElement.length === 0){
                        fieldElement = $('select[name="'+field+'"]');
                    }
                    if(field === 'skills'){
                        $('.skills-selector').addClass('is-invalid pr-0');
                    }
                    if(['description', 'company_description'].includes(field)){
                        $("trix-editor[input='"+field+"']").addClass('form-control is-invalid');
                        fieldElement.addClass('is-invalid');
                        fieldElement.parent().find('.error-holder').html(
                            `
                            <span class="invalid-feedback d-flex" role="alert">
                                <strong>${error}</strong>
                            </span>
                        `
                        );
                        return true;
                    }
                    if(['company_logo'].includes(field)){
                        $(".company-logo-wrapper").addClass('is-invalid');
                    }
                    if(['skills'].includes(field)){
                        $(".selectize-input").addClass('is-invalid');
                    }
                    fieldElement.addClass('is-invalid');
                    fieldElement.parent().append(
                        `
                            <span class="invalid-feedback" role="alert">
                                <strong>${error}</strong>
                            </span>
                        `
                    );
                });
            }
        });
    },

    /**
     * Inits skills selectize field
     */
    initSelectize: function(){
        JobCreate.skillsSelectizeInstance = $(".skills-selector").selectize({
            onChange(value) {
                JobCreate.state.skillsData = value;
            }
        });
    },

    /**
     * Populates create/edit post form with draft data
     * @returns {boolean|any}
     */
    populateDraftData: function(draftData = null){
        if(!draftData){
            draftData = localStorage.getItem('jobDraftData');
        }
        if(draftData){
            return JSON.parse(draftData);
        }
        else{
            return false;
        }
    },

    /**
     * Saves post draft data
     */
    saveDraftData: function(){
        const draftData = CreateHelper.normalizeFormData();
        localStorage.setItem('jobDraftData', JSON.stringify(draftData));
    },

    /**
     * Populates create/edit post form with draft data
     * @returns {boolean|any}
     */
    initDraft: function(data){
        JobCreate.initialDraftData = {};
        if(data){
            CreateHelper.restoreFormPerDraft(data);
        }
    },

    /**
     * Clears draft data
     */
    clearDraft: function(){
        // Clearing Fileupload class attachments
        FileUpload.attachaments = [];
        // Clearing up the local storage object
        CreateHelper.clearDraftData();
        $('#draft-clear-dialog').modal('hide');
    },

    /**
     * Clears up draft data
     * @param callback
     */
    clearDraftData: function(callback = null){
        localStorage.removeItem('jobDraftData');
        // Restoring standard input fields
        $('#job-data-form input').each(function () {
            let formField = $(this);
            const fieldName = formField.attr('name');
            if(!['skills', 'company_logo'].includes(fieldName)){
                // Trix fields
                if(['description', 'company_description'].includes(fieldName)){
                    $('trix-editor[input="'+fieldName+'"]').html('');
                }
                else{
                    formField.val('');
                }
            }
        });
        $("#type").val($("#type option:first").val());
        $("#category_id").val($("#category_id option:first").val());
        JobCreate.skillsSelectizeInstance[0].selectize.clear();
        $('.company-logo-wrapper' + ' .card-img-top').attr('src',app.companyDefaultAvatar);

        $.ajax({
            type: 'POST',
            url: app.baseUrl + '/jobs/clear/draft',
            success: function () {
                if(callback !== null){
                    callback;
                }
            },
            error: function (result) {
                launchToast('danger', trans('Error'), result.responseJSON.message);
            }
        });

    },

    /**
     * Clears up dialog (all) form errors
     */
    clearFormErrors: function () {
        // Clearing up prev form errors
        // TODO: do a *.remove is-invalid
        $('.invalid-feedback').remove();
        $('input').removeClass('is-invalid');
        $('trix-editor').removeClass('is-invalid');
        $('select').removeClass('is-invalid');
        $('div').removeClass('is-invalid');
        $(".selectize-input").removeClass('is-invalid');
        $(".company-logo-wrapper").removeClass('is-invalid');
    },

    /**
     * Serializes form data and  appends
     * other non serializable data to it
     * @returns {jQuery}
     */
    normalizeFormData: function () {
        // Filtering form data
        let data = $('#job-data-form').serializeArray();
        let filteredData = data.filter((field) => {if(field.name !== 'skills' && field.name !== '_token') return field;});
        filteredData.push({name:'skills',value: JobCreate.state.skillsData.length > 0 ? JSON.stringify(JobCreate.state.skillsData) : ''});
        filteredData.push({name:'company_logo',value:(typeof FileUpload.attachaments[0] !== 'undefined' ? JSON.stringify(FileUpload.attachaments[0]) : '')});
        filteredData.push({name:'company_type',value: JobCreate.state.companyType});
        filteredData = filteredData.map(function (field) {
            if(field.name === 'type_id') {
                // console.warn(value);
                const value = $('#type_id').find('option:selected').attr('id');
                field = {name:'type_id',value:value ? value : null};
            }
            if(field.name === 'category_id') {
                const value = $('#category_id').find('option:selected').attr('id');
                field = {name:'category_id',value:value ? value : null};
            }
            return field;
        });

        // Removing company ID if selected "new" company flow
        filteredData = filteredData.filter(function (field) {
            if(field.name === 'company_id' && JobCreate.state.companyType === 'new'){
                return false;
            }
            return true;
        });
        return filteredData;
    },

    /**
     * Restores job create form state per provided draft data
     * @param data
     */
    restoreFormPerDraft: function(data){
        // Setting up company logo
        if(data.company_logo){
            const imageData = JSON.parse(data.company_logo);
            FileUpload.attachaments.push(imageData);
        }

        if($('.selectize-input').length > 0){
            // Restoring skills
            data.map(function (field) {
                if(field.name === 'skills'){
                    let fieldValue = typeof field.value === 'string' ? JSON.parse(field.value.toString().length > 0 ? field.value.toString() : '[]') : field.value;
                    if(fieldValue.length > 0 ){
                        fieldValue.map((v)=>{
                            JobCreate.skillsSelectizeInstance[0].selectize.addItem(v, true);
                            JobCreate.state.skillsData.push(v); // Setting draft skills as current job state skills as well
                        });
                    }
                }
            });
        }

        // Restoring company logo
        data.map(function (field) {
            if(field.name === 'company_logo' && field.value !== null) {
                let fieldValue = typeof field.value === 'string' ? JSON.parse(field.value.toString() ? field.value.toString() : '{}') : field.value;
                if (Object.keys(fieldValue).length > 0) {
                    FileUpload.attachaments.push(fieldValue);
                }
            }
        });

        // Restoring standard input fields
        $('#job-data-form input').each(function () {
            let formField = $(this);
            const fieldName = formField.attr('name');
            data.map(function (field) {
                if(!['skills', 'company_logo'].includes(field.name)){

                    if(field.name === fieldName){
                        // Trix fields
                        if(['description', 'company_description'].includes(field.name)){
                            $('trix-editor[input="'+field.name+'"]').html(field.value);
                        }
                        // <input> fields
                        else{
                            formField.val(field.value);
                        }
                    }

                    if(field.name === 'type_id'){
                        $('#type_id option').filter(function(){
                            if(!field.value){
                                return false;
                            }
                            return this.id.toString() === field.value.toString();
                        }).prop('selected', true);
                    }

                    if(field.name === 'category_id'){
                        $('#category_id option').filter(function(){
                            if(!field.value){
                                return false;
                            }
                            return this.id.toString() === field.value.toString();
                        }).prop('selected', true);
                    }
                }
            });

        });

    },

    /**
     * Toggles between new and existing companies selectors
     * @param type
     */
    toggleCompanyCreateSelector: function (type) {
        JobCreate.state.companyType = type;
        if(type === 'new'){
            $('.new-company').removeClass('d-none');
            $('.existing-company').addClass('d-none');
            $('.new-company-label').addClass('d-none');
            $('.existing-company-label').removeClass('d-none');
        }
        else{
            $('.new-company').addClass('d-none');
            $('.existing-company').removeClass('d-none');
            $('.new-company-label').removeClass('d-none');
            $('.existing-company-label').addClass('d-none');
        }

    },

    /**
     * Initializing (existing) companies selector and restoring form state per draft if needed
     * @param draftData
     */
    restoreCompanySelectorState: function (draftData) {
        if(typeof user !== 'undefined' && user.companiesCount > 0){
            if(draftData){
                draftData.map(function (field) {
                    if (field.name === 'company_id' && field.value !== null) {
                        JobCreate.state.companyType = draftData.company_type;
                    }
                });
            }
            else{
                JobCreate.state.companyType = 'existing';
            }
            CreateHelper.toggleCompanyCreateSelector(JobCreate.state.companyType);
            Company.initSelectizeUserList(draftData);
        }
    },

    /**
     * Opens up the login dialog
     */
    openLoginDialog: function () {
        $('#login-dialog').modal('show');
    },

    /**
     * Inits the "Clear draft" button event
     */
    initDraftClearButton: function () {
        $('.draft-clear-button').on('click',function () {
            $('#draft-clear-dialog').modal('show');
        });
    },


};
