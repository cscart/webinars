{$offline_sale_id = $offline_sale_id|default:0}
{$offline_sale_data = $offline_sale_data|default:[]}

{* Заголовок страницы *}
{capture name = "title"}
    {if $offline_sale_id}
        {$offline_sale_data.title}
    {else}
        {__("offline_sales.new_sale")}
    {/if}
{/capture}

{* Основное содержимое страницы *}
{capture name = "mainbox"}
    {* На странице находится форма со вкладками *}
    {capture name = "tabsbox"}
        <form class="form-horizontal form-edit"
              method="post"
              name="offline_sale_form"
              id="offline_sale_form"
              enctype="multipart/form-data"
              action="{fn_url("")}"
        >
            {* ID редактируемой распродажи *}
            <input type="hidden"
                   name="offline_sale_id"
                   value="{$offline_sale_id}"
            />

            {* Параметр для хранения выбранной вкладки *}
            <input type="hidden"
                   name="selected_section"
                   id="selected_section"
                   value="{$smarty.request.selected_section}"
            />

            {* Вкладка General *}
            <div id="content_general" class="hidden">
                <div class="control-group">
                    <label for="elm_title" class="control-label cm-required">
                        {__("offline_sales.sale_title")}
                    </label>
                    <div class="controls">
                        <input type="text"
                               id="elm_title"
                               class="input-large"
                               name="offline_sale_data[title]"
                               value="{$offline_sale_data.title|default:""}"
                        />
                    </div>
                </div>

                <div class="control-group">
                    <label for="elm_description" class="control-label cm-required">
                        {__("offline_sales.sale_description")}
                    </label>
                    <div class="controls">
                        <textarea class="cm-wysiwyg"
                                  id="elm_description"
                                  name="offline_sale_data[description]"
                        >{$offline_sale_data.description|default:""}</textarea>
                    </div>
                </div>

                {* Выбор магазина реализован через object picker *}
                <div class="control-group">
                    <label for="elm_store" class="control-label cm-required">
                        {__("offline_sales.store")}
                    </label>
                    <div class="controls">
                        <select class="cm-object-picker"
                                id="elm_store"
                                data-ca-object-picker-ajax-url="{fn_url("offline_sales.get_stores")}"
                                data-ca-object-picker-placeholder="{__("offline_sales.find_store")}"
                                data-ca-object-picker-placeholder-value="0"
                                name="offline_sale_data[store_id]"
                        >
                            <option value="{$offline_sale_data.store_id|default:"0"}"></option>
                        </select>
                    </div>
                </div>

                {* Для управления изображениями используется готовый компонент *}
                <div class="control-group">
                    <label for="elm_image" class="control-label">
                        {__("offline_sales.sale_image")}
                    </label>
                    <div class="controls">
                        {**
                         * Компонент для загрузки изображений.
                         * image_name — имя группы элементов для загрузки изображения.
                         *     Должен совпадать с $name в вызове fn_attach_image_pairs()
                         * image_object_type — тип объекта изображения.
                         *     Должен совпадать с $object_type в вызове fn_attach_image_pairs(), fn_get_image_pairs() и fn_delete_image_pairs()
                         * image_object_id — ID объекта, к которому привязано изображение.
                         *     Должен совпадать с $object_id в вызове fn_attach_image_pairs(), fn_get_image_pairs() и fn_delete_image_pairs()
                         * image_pair — данные изображение, полученные из fn_get_image_pairs()
                         * no_detailed — определяет, нужно ли выводить отдельный элемент для загрузки детального изображения
                         * icon_title — заголовок над полем загрузки иконки
                         * detailed_title — заголовок над полем загрузки детального изображения
                         *}
                        {include file = "common/attach_images.tpl"
                            image_name = "offline_sale"
                            image_object_type = "offline_sale"
                            image_pair = $offline_sale_data.main_pair
                            image_object_id = $offline_sale_id
                            no_detailed = false
                            icon_title = __("offline_sales.icon_will_be_shown_in_blocks")
                            detailed_title = __("offline_sales.image_will_be_shown_on_sale_pages")

                        }
                    </div>
                </div>

                {* Покажет поле редактирования SEO-имени при наличии модуля SEO *}
                {if $addons.seo.status === "ObjectStatuses::ACTIVE"|enum}
                    {**
                     * Компонент для задания SEO-имени.
                     * hide_title — определяет, нужно ли скрывать заголовок "SEO"
                     * object_id — идентификатор объекта, для которого генерируется SEO-имя
                     * object_type — тип объекта, для которого генерируется SEO-имя (должен совпадать с тем, что прописано в схеме)
                     * object_name — название параметра-массива, который хранит данные редактируемого объекта
                     * object_data — данные редактируемого объекта
                     *}
                    {include file = "addons/seo/common/seo_name_field.tpl"
                        hide_title = true
                        object_id = $offline_sale_data.offline_sale_id
                        object_type = "\OfflineSales\Service::SEO_OBJECT_TYPE"|constant
                        object_name = "offline_sale_data"
                        object_data = $offline_sale_data
                    }
                {/if}
            </div>

            {* Вкладка с товарами *}
            <div id="content_products" class="hidden">
                {include file = "pickers/products/picker.tpl"
                    data_id = "offline_sale_products"
                    input_name = "offline_sale_data[product_ids]"
                    item_ids = $offline_sale_data.product_ids
                    type = "links"
                    placement = "right"
                }
            </div>
        </form>
    {/capture}

    {* Выводит вкладки *}
    {include file = "common/tabsbox.tpl"
        content = $smarty.capture.tabsbox
        active_tab = $smarty.request.selected_section
        track = true
    }
{/capture}

{* Дополнительные действия с сущностью *}
{capture name="buttons"}
    {capture name="tools_list"}
        {if $offline_sale_id}
            <li>
                {btn type = "list"
                    text = __("delete")
                    class = "cm-confirm"
                    href = "offline_sales.delete?offline_sale_id={$offline_sale_id}"
                    method = "POST"
                }
            </li>
        {/if}
    {/capture}
    {dropdown content = $smarty.capture.tools_list}
{/capture}

{**
 * Кнопки основных действий с сущностью.
 * but_target_form — указывает ID формы, которую должна сохранять кнопка "Save"
 * but_name — содержит маршрут, на который будет отправлен запрос с формы
 * save — флаг, который определяет, будет ли кнопка сохранения выводить текст "Save" или "Create"
 *}
{capture name = "adv_buttons"}
    {include file = "buttons/save_cancel.tpl"
        but_name = "dispatch[offline_sales.update]"
        but_target_form = "offline_sale_form"
        save = $offline_sale_id
    }
{/capture}

{* Отображает страницу, показывает на ней переключатель языков *}
{include file = "common/mainbox.tpl"
    title = $smarty.capture.title
    content = $smarty.capture.mainbox
    buttons = $smarty.capture.buttons
    adv_buttons = $smarty.capture.adv_buttons
    select_languages = true
}
