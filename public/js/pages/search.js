/**
 * Search page component
 */
"use strict";
/* global selectedSkills */

$(function () {
    Search.skillsSelectizeInstance = $(".skills-selector").selectize({
        onChange(value) {
            Search.state.skillsData = value;
        }
    });
    if(selectedSkills.length){
        selectedSkills.map((v)=>{
            Search.skillsSelectizeInstance[0].selectize.addItem(v, true);
            Search.state.skillsData.push(v); // Setting draft skills as current job state skills as well
        });
    }
    Search.initSearchFilterLiveReloads();
});

var Search = {

    skillsSelectizeInstance: null,
    state : {
        skillsData: [],
    },

    /**
     * Binds inputs changes to search refresh
     */
    initSearchFilterLiveReloads: function () {
        $('.search-filters-form input, .search-filters-form select').on('change',function () {
            const attr = $(this).attr('id');
            if(attr !== 'skills'){
                $('.search-filters-form').submit();
            }
        });
        $('.search-filters-form input').keypress(function (e) {
            if (e.which === 13) {
                $('.search-filters-form').submit();
                return false;
            }
        });
    }

};
