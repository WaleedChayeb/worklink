/*
* Post create page
 */
"use strict";
/* global CreateHelper, Company, companyData, ctrlSaveInit */

$(function () {

    const parsedCompanyData = CompanyEdit.parseCompanyData();
    CreateHelper.initDraft(parsedCompanyData,'edit');
    Company.initCompanyLogoUploader('.company-logo-wrapper','/attachment/upload/company');
    CreateHelper.initFormSave('edit');

    // CTRL+S Override
    ctrlSaveInit('#job-data-form');

});


var CompanyEdit = {

    /**
     * Parses backend object to JS draft normalized one
     * @returns {[]}
     */
    parseCompanyData: function () {
        let logoValue = {};
        let parsedCompanyData = [];
        for ( var key in companyData ) {
            if(key === 'logo_attachment'){
                logoValue = companyData.logo_attachment;
            }
            parsedCompanyData.push({name:`company_${key}`,value:companyData[key]});
        }
        parsedCompanyData.push({name:`company_logo`,value:logoValue});
        return parsedCompanyData;
    }

};
