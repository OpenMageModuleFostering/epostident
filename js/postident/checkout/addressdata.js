/**
 * @category   DeutschePost Postident
 * @package    DeutschePost_Postident
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @author     Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright  Copyright (c) 2012 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var PostidentAddressData = Class.create();
PostidentAddressData.prototype = {
    initialize: function(postident_address_data) {
        this.postidentData = postident_address_data.evalJSON();
        // check if givenname exist in JSON object, log Error if not
        if (!this.postidentData.hasOwnProperty('givenname')) {
            console.log("Error parsing postidentData JSON object, key 'givenname' doesn't exists");
        }

        /*
         * map magento_form_keys to postident_data_keys
         * 
         * magento_form_key : postident_data_key
         */
        this.mappedAddressArray = {
            'firstname': 'givenname',
            'lastname': 'familyname',
            'city': 'city',
            'postcode': 'zipcode',
            'country_id': 'nationality'
        };
    },
    updateFormData: function(addressType) {

        if (false === this.hasPostidentData()) {
            return false;
        }

        this.updateBirthDateFormFields(addressType);
        this.updatePrefixFormField(addressType);
        this.updateStreetFormField(addressType);
        for (var magentoAddressKey in this.mappedAddressArray) {
            var postidentKey = this.mappedAddressArray[magentoAddressKey];
            if (true === this.postidentData.hasOwnProperty(postidentKey)
                    && this.postidentData[postidentKey] !== null) {

                if ($(addressType + ":" + magentoAddressKey) !== null) {
                    $(addressType + ":" + magentoAddressKey).value = this.postidentData[postidentKey];
                }
            }
            else {
                continue;
            }
        }
        if (addressType === 'billing') {
            billingRegionUpdater.update();
        }
        if (addressType === 'shipping') {
            shippingRegionUpdater.update();
        }
    },
    updatePrefixFormField: function(addressType) {

        if ($(addressType + ":" + 'prefix') !== null) {
            $(addressType + ":" + 'prefix').value = this.getPrefix();
        }
    },
    updateStreetFormField: function(addressType) {

        if ($(addressType + ":" + 'street1') !== null) {
            $(addressType + ":" + 'street1').value = this.getStreetWithHouseNumber();
        }

        if (typeof(this.postidentData['addressaddon']) !== "undefined") {
            $(addressType + ":" + 'street2').value = this.postidentData['addressaddon'];
        }

    },
    updateBirthDateFormFields: function(addressType) {

        if ($(addressType + ":" + 'day') !== null
                && $(addressType + ":" + 'month') !== null
                && $(addressType + ":" + 'year') !== null) {

            var date = this.getBirthdayDateObject();
            $(addressType + ":" + 'day').value = date.getDate();
            $(addressType + ":" + 'month').value = date.getMonth() + 1;
            $(addressType + ":" + 'year').value = date.getFullYear().toString();

        }
    },
    checkFormFieldsEmpty: function(addressType) {

        if (false === this.hasPostidentData()) {
            return false;
        }

        //iterate over mapping array which covers the usual field names
        for (var magentoAddressKey in this.mappedAddressArray) {

            if (null === $(addressType + ":" + magentoAddressKey)
                    || "country_id" === magentoAddressKey) {
                continue;
            }

            if (false === $(addressType + ":" + magentoAddressKey).value.empty()) {
                return false;
            }
        }

        //for street1 form field
        if (null !== $(addressType + ":" + 'street1') && false === $(addressType + ":" + 'street1').value.empty()) {
            return false;
        }

        //for birthdate form fields
        if (null !== $(addressType + ":" + 'day') && false === $(addressType + ":" + 'day').value.empty()) {
            return false;
        }
        if (null !== $(addressType + ":" + 'month') && false === $(addressType + ":" + 'month').value.empty()) {
            return false;
        }
        if (null !== $(addressType + ":" + 'year') && false === $(addressType + ":" + 'year').value.empty()) {
            return false;
        }
        // for prefix form field
        //Excluded prefix field - returns false in case of predefined prefix option
        /*
        if (null !== $(addressType + ":" + 'prefix') && false === $(addressType + ":" + 'prefix').value.empty()) {
            return false;
        }
        */
        return true;
    },
    checkFormFieldsAreSame: function(addressType) {

        if (false === this.hasPostidentData()) {
            return false;
        }

        //iterate over mapping array which covers the usual field names
        for (var magentoAddressKey in this.mappedAddressArray) {
            var postidentKey = this.mappedAddressArray[magentoAddressKey];

            if (null === $(addressType + ":" + magentoAddressKey)) {
                continue;
            }

            if (this.postidentData[postidentKey] !== $(addressType + ":" + magentoAddressKey).value) {
                return false;
            }
        }

        if (null !== $(addressType + ":" + 'street1') 
                && this.getStreetWithHouseNumber() !== $(addressType + ":" + 'street1').value){
            return false;
        }

        //for birthdate form fields
        if (null !== $(addressType + ":" + 'day')
                && this.getBirthdayDateObject().getDate().toString() !== $(addressType + ":" + 'day').value) {
            return false;
        }

        // add 1 since getMonth() returns months from 0 - 11
        var month = this.getBirthdayDateObject().getMonth() + 1;
        if (null !== $(addressType + ":" + 'month')
                && month.toString() !== $(addressType + ":" + 'month').value) {
            return false;
        }

        if (null !== $(addressType + ":" + 'year')
                && this.getBirthdayDateObject().getFullYear().toString() !== $(addressType + ":" + 'year').value) {
            return false;
        }

        // for prefix form field
        if (null !== $(addressType + ":" + 'prefix')
                && this.getPrefix() !== $(addressType + ":" + 'prefix').value) {
            return false;
        }

        return true;
    },
    getBirthdayDateObject: function() {

        if (typeof(this.postidentData['dateofbirth']) !== "undefined") {
            var a = this.postidentData['dateofbirth'].split(" ");
            var d = a[0].split("-");

            return new Date(d[0], (d[1] - 1), d[2]);
        }
    },
    getPrefix: function() {

        if (typeof(this.postidentData['salutation']) !== "undefined") {
            return Translator.translate(this.postidentData['salutation']);
        }
    },
    getStreetWithHouseNumber: function() {
        var street = '';
        if (typeof(this.postidentData['street']) !== "undefined"
                && typeof(this.postidentData['housenumber']) !== "undefined") {
            street = this.postidentData['street'] + " " + this.postidentData['housenumber'];
        } 
        return street;
       
    },
    hasPostidentData: function() {

        if (typeof(this.postidentData) !== "undefined"
                && this.postidentData.hasOwnProperty('givenname')) {
            return true;
        } else {
            if (window['console'] !== 'undefined')
                console.log("Error: postidentData is undefined or corrupted since givenname is missing");
            return false;
        }
    }
};