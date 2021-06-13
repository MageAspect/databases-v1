export class FormsTableController {

    constructor($tableElement) {
        this.$tableElement = $tableElement;
    }

    activateReloadButton() {
        let $reloadButton = this.$tableElement.querySelector('.reload');

        $reloadButton.addEventListener(
            'click',
            (e) => {
                e.preventDefault();
                this.reloadAllEntries()
            }
        );
    }

    activateControlButtons() {
        let $editControlButtons = this.$tableElement.querySelectorAll('.controls div[data-action="edit"]');
        let $deleteControlButtons = this.$tableElement.querySelectorAll('.controls div[data-action="delete"]');

        for (let $button of $editControlButtons) {
            $button.addEventListener(
                'click',
                (e) => {
                    e.preventDefault();
                    let entryId = e.currentTarget.parentElement.dataset.entryId;
                    this.editEntryAction(entryId)
                }
            )
        }

        for (let $button of $deleteControlButtons) {
            $button.addEventListener(
                'click',
                (e) => {
                    e.preventDefault();
                    let entryId = e.currentTarget.parentElement.dataset.entryId;
                    this.deleteEntryAction(entryId).then(
                        (e) => this.reloadAllEntries()
                    );
                }
            )
        }
    }

    editEntryAction(entryId) {
        window.location.href = '/admin/form/' + entryId + '/edit/';
    }

    async deleteEntryAction(entryId) {
        let response = await fetch(
            '/ajax/admin/feedback.delete',
            {
                method: 'POST',
                body: JSON.stringify(
                    {
                        id: entryId
                    }
                ),
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                }
            }
        );

        if (!response.ok) {
            alert('Ошибка удаления записей!');
        }
    }

    async reloadAllEntries() {
        let entriesData = await this.getEntriesDataAction();

        let tbody = this.$tableElement.querySelector('tbody');

        tbody.innerHTML = '';

        let controls = '' +
            '<div class="controls" data-entry-id="">' +
            '<div data-action="edit">' +
            '<img src="/public/assets/img/edit.png" title="Редактировать">' +
            '</div>' +
            '<div data-action="delete">' +
            '<img src="/public/assets/img/delete.png" title="Удалить">' +
            '</div>' +
            '</div>';

        if (!Array.isArray(entriesData) || entriesData.length === 0) {
            tbody.innerHTML = '' +
                '<tr>' +
                '<td class="empty-data" colspan="9">Нет данных по обратным связям!</td>' +
                '</tr>';
            return;
        }

        for (let entry of entriesData) {
            let tr = document.createElement('tr');
            tr.className = 'item';

            let controlsTd = document.createElement('td');
            controlsTd.className = 'controls-wrapper';
            controlsTd.innerHTML = controls;
            controlsTd.querySelector('.controls').dataset.entryId = entry.id;

            tr.append(controlsTd);

            for (let key in entry) {
                let td = document.createElement('td');
                td.className = 'item-data';

                if (entry.hasOwnProperty(key)) {
                    td.textContent = entry[key];
                }
                tr.append(td);
            }
            tbody.append(tr);
        }
        this.activateControlButtons();
    }

    async getEntriesDataAction() {
        let response = await fetch('/ajax/admin/feedbacks.get');

        if (!response.ok) {
            alert('Ошибка обновления записей!');
            return;
        }

        let responseData = await response.json();
        if (responseData.hasOwnProperty('result')) {
            return responseData.result;
        }
        return null;
    }
}