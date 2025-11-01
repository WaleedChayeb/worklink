/**
 * Create job page component
 */
"use strict";
/* global CreateHelper, Company, ctrlSaveInit */

$(function () {
    CreateHelper.initFormSave('create');
    Company.initCompanyLogoUploader('.company-logo-wrapper','/attachment/upload/company');
    // CTRL+S Override
    ctrlSaveInit('#job-data-form');
});
