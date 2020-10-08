{* Основное содержимое страницы *}
{capture name="mainbox"}
    {* Выводит паджинацию в начале списка *}
    {include file = "common/pagination.tpl"}
    {if $offline_sales}
        {* Выводит данные в responsive таблице *}
        <div class="table-responsive-wrapper">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>{__("offline_sales.sale_title")}</th>
                    <th width="10%">&nbsp;</th>
                    <th class="right" width="7%">{__("status")}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $offline_sales as $sale}
                    <tr class="cm-row-status-{$sale.status|lower}">
                        <td data-th="{__("offline_sales.sale_title")}">
                            <a href="{fn_url("offline_sales.update?offline_sale_id={$sale.offline_sale_id}")}">
                                {$sale.title}
                            </a>
                        </td>
                        <td data-th="{__("tools")}">
                            {* Формирует контекстное меню для объекта *}
                            {capture name="tools_list"}
                                <li>
                                    {btn type = "list"
                                        text = __("edit")
                                        href = "offline_sales.update?offline_sale_id={$sale.offline_sale_id}"
                                    }
                                </li>
                                <li>
                                    {btn type = "list"
                                        text = __("delete")
                                        class = "cm-confirm"
                                        href = "offline_sales.delete?offline_sale_id={$sale.offline_sale_id}"
                                        method = "POST"
                                    }
                                </li>
                            {/capture}
                            {* Выводит контекстное меню *}
                            <div class="hidden-tools">
                                {dropdown content = $smarty.capture.tools_list}
                            </div>
                        </td>
                        <td class="nowrap right" data-th="{__("status")}">
                            {* Выводит переключатель статусов *}
                            {include file = "common/select_popup.tpl"
                                object_id_name = "offline_sale_id"
                                table = "offline_sales"
                                id = $sale.offline_sale_id
                                status = $sale.status
                                hidden = false
                                popup_additional_class = "dropleft"
                            }
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <p class="no-items">{__("no_data")}</p>
    {/if}

    {* Выводит паджинацию в конце списка *}
    {include file = "common/pagination.tpl"}
{/capture}

{* Кнопки основных действий с сущностью *}
{capture name="adv_buttons"}
    {include file = "common/tools.tpl"
        tool_href = "offline_sales.add"
        title = __("offline_sales.new_sale")
    }
{/capture}

{* Сайдбар на странице *}
{capture name="sidebar"}
    <div class="sidebar-row">
        <h6>{__("search")}</h6>
        <form action="{""|fn_url}"
              name="offline_sales_search_form"
              method="get"
        >
            {* Простая форма поиска — выводится в сайдбаре *}
            {capture name="simple_search"}
                <div class="control-group">
                    <label for="elm_status">{__("status")}:</label>
                    <div class="break">
                        <select class="cm-object-picker"
                                name="status"
                                id="elm_status"
                                data-ca-object-picker-placeholder="{__("all")}"
                                data-ca-object-picker-allow-clear="true"
                        >
                            <option value="">{__("all")}</option>
                            <option value="{"ObjectStatuses::ACTIVE"|enum}"
                                    {if $search.status === "ObjectStatuses::ACTIVE"|enum}selected{/if}
                            >{__("active")}</option>
                            <option value="{"ObjectStatuses::DISABLED"|enum}"
                                    {if $search.status === "ObjectStatuses::DISABLED"|enum}selected{/if}
                            >{__("disabled")}</option>
                        </select>
                    </div>
                </div>
            {/capture}

            {* Расширенная форма поиска — выводится во всплывающем окне *}
            {capture name="advanced_search"}
                <div class="group">
                    <div class="control-group">
                        <label for="elm_store" class="control-label">
                            {__("offline_sales.store")}
                        </label>
                        <div class="controls">
                            <select class="cm-object-picker"
                                    id="elm_store"
                                    data-ca-object-picker-ajax-url="{fn_url("offline_sales.get_stores")}"
                                    data-ca-object-picker-placeholder="{__("all")}"
                                    data-ca-object-picker-placeholder-value="0"
                                    data-ca-object-picker-allow-clear="true"
                                    name="store_id"
                            >
                                <option value="{$search.store_id|default:"0"}"></option>
                            </select>
                        </div>
                    </div>
            {/capture}

            {* Выводит форму поиска *}
            {include file = "common/advanced_search.tpl"
                simple_search = $smarty.capture.simple_search
                advanced_search = $smarty.capture.advanced_search
                dispatch = "offline_sales.manage"
                not_saved = true
            }
        </form>
    </div>
{/capture}

{* Собирает все компоненты страницы через компоновщик *}
{include file = "common/mainbox.tpl"
    title = __("offline_sales.offline_sales")
    content = $smarty.capture.mainbox
    adv_buttons = $smarty.capture.adv_buttons
    sidebar = $smarty.capture.sidebar
    select_languages = true
}
