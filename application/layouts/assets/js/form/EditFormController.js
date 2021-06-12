export class EditFormController {
    constructor($formElement) {
        this.$formElement = $formElement;
    }

    insertFormFields($fields) {
        $fields = this.unsetExtraFields($fields);

        for (let fieldName in $fields) {

            if (!$fields.hasOwnProperty(fieldName)) {
                continue;
            }
            let fieldValue = $fields[fieldName];
            let $fieldElement = this.$formElement.querySelector('[name="' + fieldName + '"]');

            if (fieldName.includes('user_access_') && +$fields[fieldName] > 0) {
                this.$formElement.querySelector('#' + fieldName).checked = true;
            } else if (fieldName === 'do_next') {
                let options = $fieldElement.querySelectorAll('option');
                for (let item of options) {
                    if (item.value === fieldValue) {
                        item.selected = true;
                    }
                }
            } else if (fieldName === 'mark') {
                let radioButtons = this.$formElement.querySelectorAll('[name="' + fieldName + '"]');
                for (let item of radioButtons) {
                    if (item.value === fieldValue) {
                        item.checked = true;
                    }
                }
            } else if ($fieldElement) {
                $fieldElement.value = fieldValue;
            }
        }
    }

    unsetExtraFields($fields) {
        delete $fields['id'];
        delete $fields['date_create'];

        return $fields;
    }
}