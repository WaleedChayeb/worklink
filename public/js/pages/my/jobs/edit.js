/*
* Post create page
 */
"use strict";
/* global app, CreateHelper, AiSuggestions, jobData, ctrlSaveInit */

$(function () {
    CreateHelper.initSelectize();
    // CreateHelper.initSubCategoriesSwitch();
    const parsedJobData = JobEdit.parseJobData();
    CreateHelper.initDraft(parsedJobData,'edit');
    CreateHelper.initFormSave('edit');

    if(app.open_ai_enabled){
        AiSuggestions.initAISuggestions('#description', 'trix');
    }

    // CTRL+S Override
    ctrlSaveInit('#job-data-form');

});

var JobEdit = {

    /**
     * Parses backend object to JS draft normalized one
     * @returns {[]}
     */
    parseJobData: function () {
        let parsedJobData = [];
        for ( var key in jobData ) {
            parsedJobData.push({name:`${key}`,value:jobData[key]});
        }
        return parsedJobData;
    }
};
