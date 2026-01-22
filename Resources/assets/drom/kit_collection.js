/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 */

/** Лимит элементов коллекции */
limitDromKitCollectionItems = 4;

/** глобальный объект для отслеживания удаленных элементов коллекции */
deletedDromKitCollectionItems = new Map();

/** глобальный индекс текущего элемента коллекции */
DromKitCollectionItemKey = 0;

executeFunc(function initDromTokenKits()
{
    /** @type {FormData} */
    const form = new FormData(document.forms.drom_token_new_edit_form);

    /** @type {HTMLFormElement} */
    const dromTokenFormHTMLs = document.forms.drom_token_new_edit_form;

    /**
     * @type {HTMLButtonElement}
     * кнопка добавления элемента в коллекцию
     * */
    const kitCollectionBtnAdd = document.getElementById('kit_сollection_add');

    kitCollectionBtnAdd.addEventListener('click', addKitItem);

    /** кнопка удаления */
    const currentDeleteBtns = document.getElementById('kit_сollection').querySelectorAll('.del-item-kit');

    if(currentDeleteBtns)
    {
        deleteKitItem(currentDeleteBtns)
    }

    return true;
});

function addKitItem()
{
    /**
     * @type {HTMLDivElement}
     * блок с элементами коллекции
     * */
    const kitCollection = document.getElementById('kit_сollection');

    /** @type {number} */
    DromKitCollectionItemKey = this.dataset.index

    /** проверка на наличие ранее удаленных элементов */
    if(deletedDromKitCollectionItems.size > 0)
    {

        let last = '';

        /** получаем последний индекс удаленного элемента */
        deletedDromKitCollectionItems.forEach(function(value)
        {
            last = value;
        });

        /**
         * @type {number}
         * меняем глобальный индекс текущего элемента коллекции на индекс удаленного элемента */
        DromKitCollectionItemKey = last

        /** удаляем элемент из хранилища удаленных элементов */
        deletedDromKitCollectionItems.delete('key' + last)
    }

    /**
     * @type {string}
     * id прототипа из кнопки добавления элемента в коллекцию
     * */
    const prototypeName = this.dataset.prototype;

    /**
     * @type {HTMLDivElement}
     * элемент с прототипом
     * */
    const prototypeElement = document.getElementById(prototypeName);

    /**
     * @type {string}
     *  контент прототипа
     * */
    let prototypeContent = prototypeElement.dataset.prototype;

    /**
     * @type {number}
     * увеличиваем индекс элемента коллекции
     * */
    let index = parseInt(this.dataset.index) + 1;

    /** добавляем текущее значение к кнопке для отслеживания увеличения элементов коллекции */
    this.setAttribute('data-index', index)


    // ограничение максимального количество элементов коллекции
    if(parseInt(this.dataset.index) > limitDromKitCollectionItems)
    {
        this.setAttribute('data-index', limitDromKitCollectionItems)
        return;
    }

    /** Добавление индекса для элемента коллекции по заполнителю из формы prototype_name => '__kit__' */
    prototypeContent = prototypeContent.replace(/__kit__/g, DromKitCollectionItemKey);

    const parser = new DOMParser();

    /** @type {Document} */
    const result = parser.parseFromString(prototypeContent, 'text/html');

    /**
     * @type {HTMLDivElement}
     * элемент с прототипом и индексами для элемента коллекции по заполнителю из формы prototype_name => '__kit__'
     * */
    const prototypItem = result.getElementById('prototypeItem').querySelector('.item-kit');

    /** вставляем элемент в коллекцию */
    kitCollection.append(prototypItem);

    /** @type {NodeListOf<HTMLButtonElement>} */
    const deleteBtns = kitCollection.querySelectorAll('.del-item-kit');

    deleteKitItem(deleteBtns)
}

function deleteKitItem(buttons)
{
    buttons.forEach(function(btn)
    {
        btn.addEventListener('click', function()
        {
            /**
             * @type {string}
             * */
            let deteteItemId = btn.id.replace(/delete-/g, '');

            /**
             * @type {HTMLDivElement}
             * элемент для удаления
             * */
            const itemForDelete = document.getElementById(deteteItemId);

            /**
             * @type {string}
             * индекс для удаления
             * */
            const deleteIndex = btn.id.replace(/delete-drom_token_form_kit-/g, '');
            // const deleteIndex = parseInt(btn.id.match(/\d+/));

            /** добавляем индекс удаленного элемента для отслеживания */
            deletedDromKitCollectionItems.set('key' + deleteIndex, deleteIndex)

            /**
             * если элемент удалился - получаем текущий индекс коллекции и уменьшаем его в кнопке добавления элементов
             * */
            if(itemForDelete)
            {

                let addBtn = document.getElementById('kit_сollection_add');
                const newIndex = parseInt(addBtn.dataset.index) - 1;

                addBtn.setAttribute('data-index', newIndex)

                itemForDelete.remove()
            }
        });
    });
}