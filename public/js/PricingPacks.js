/**
 * Login modal - Used for easy login from profile page
 */
"use strict";
/* global plans, setCookie */

$(function () {

    // Setting default or draft selected pack
    let defaultPack = plans.defaultPack;
    if(plans.updatingFromPack){
        defaultPack = plans.updatingFromPack;
    }
    PricingPacks.setPricingPack(defaultPack);

    // Initing the price changer
    PricingPacks.initPricingPackPicker();
});

var PricingPacks = {

    selectedPack: null,

    /**
     * Instantiates the pricing packs selector
     */
    initPricingPackPicker: function(){
        $('.pricing-table').on('click', function () {
            setTimeout(function(){
                $('.pricing-table').removeClass('active');
            }, 10);
            let element = $(this);
            setTimeout(function(element){
                PricingPacks.setPricingPack(element.attr('data-package-id'));
                element.addClass('active');
            }, 50, element);
        });
    },

    /**
     * Selects the desired pricing pack
     * @param value
     * @returns {boolean}
     */
    setPricingPack: function (value) {
        if(PricingPacks.selectedPack !== value){
            PricingPacks.selectedPack = value;
            $('.pricing-table[data-package-id="'+value+'"]').addClass('active');
        }
        else{
            setTimeout(function(){
                $('.pricing-table').removeClass('active');
            }, 10);
            PricingPacks.selectedPack = null;
        }
        setCookie('selectedPack', value, 365);
        return true;
    }

};

// Saving draft data before unload
window.addEventListener('beforeunload', function () {
    PricingPacks.setPricingPack(PricingPacks.selectedPack);
});
