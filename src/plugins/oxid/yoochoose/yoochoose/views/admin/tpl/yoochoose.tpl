[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript" src='[{$oViewConf->getResourceUrl()}]js/libs/jquery.min.js'></script>
<script type="text/javascript" src='[{$oViewConf->getResourceUrl()}]js/libs/jquery-ui.min.js'></script>
<script type="text/javascript" src='[{$oViewConf->getModuleUrl("yoochoose","/out/js/oxinputvalidator.js")}]'></script>
<script>
    $('document').ready(function () {
        $('form.js-oxValidate').oxInputValidator();
    });
</script>

<script type="text/javascript">
    if (top) {
        top.sMenuItem = "[{oxmultilang ident="YOOCHOOSE_MENUITEM" }]";
        top.sMenuSubItem = "[{oxmultilang ident="YOOCHOOSE_MENUSUBITEM" }]";
        top.sWorkArea = "[{$_act}]";
        top.setTitle();
    }

    yc_register = function () {

        var form = document.createElement("form"),
                input = document.createElement("input"),
                object = [{$obj}];

        form.method = "post";
        form.action = "[{$ycLink}]";
        form.target = "_blank";

        for (var property in object) {
            if (object.hasOwnProperty(property)) {
                input.setAttribute('name', property);
                input.setAttribute('value', object[property]);
                form.appendChild(input.cloneNode(true));
            }
        }

        form.submit();
    };

    yc_overwrite = function () {
        var checkbox = document.getElementById('overwriteCheckbox'),
                inputs = document.getElementsByClassName('readonly');
        for (var i = 0; i < inputs.length; i++) {
            if (checkbox.checked) {
                inputs[i].readOnly = false;
                checkbox.value = 1;
            } else {
                inputs[i].readOnly = true;
                checkbox.value = 0;
            }
        }
    };

    yc_cutomerid_changed = function (el) {
        var link = document.getElementById('yoochoose-admin-link');

        link.href = link.href.replace(/(.*=)(\d*)/g, function () {
            return arguments[1] + el.value;
        });
    };
</script>

<style>
    table tr th {
        font-family: inherit;
        font-size: 1.7em;
        color: firebrick;
    }

    table tr td  {
        max-width: 400px;
    }

    table {
        border-collapse: collapse !important;
    }

    td {
        padding-bottom: 1em !important;
    }

    .extra-info {
        font-size: smaller;
        width: 100%;
        white-space: normal;
    }
</style>


<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>

<div id="ycMessages">
    [{if isset($ycSuccessMessage) }]
        <h2 style="color: forestgreen;">[{oxmultilang ident="YOOCHOOSE_SUCCESS" }]</h2>
    [{/if}]
    [{if isset($ycErrorMessage) }]
        <h2 style="color: firebrick;">[{oxmultilang ident="YOOCHOOSE_ERROR" }]</h2>
        [{if isset($ycErrorMessageText) }]
            <h2 style="color: firebrick;">[{$ycErrorMessageText}]</h2>
        [{/if}]
    [{/if}]
</div>

<table cellspacing="0" cellpadding="0" border="0" width="98%">

    <tr>
        <td valign="top" class="edittext">
            <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" class="js-oxValidate"
                  method="post">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="yoochoose">
                <input type="hidden" name="fnc" value="saveConfigForm">
                <input type="hidden" name="oxid" value="yoochoose">
                <input type="hidden" name="editval[oxshops__oxid]" value="">
                <table cellspacing="0" cellpadding="0" border="0">

                    <tr>
                        <th class="edittext" width="180" height="40">
                            [{oxmultilang ident="YOOCHOOSE_GENERAL_SETTINGS_TITLE" }]
                        </th>
                    </tr>

                    <tr>
                        <td class="edittext" width="180" height="40">

                        </td>
                        <td class="edittext">
                            <button type="button" onclick="yc_register();">[{oxmultilang
                                ident="YOOCHOOSE_GENERAL_REGISTER_BUTTON" }]
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycCustomerId">[{oxmultilang ident="YOOCHOOSE_CONFIG_CUSTOMERID"}]</label>
                        </td>
                        <td class="edittext">
                            <input type="text" id="ycCustomerId" class="editinput js-oxValidate js-oxValidate_notEmpty" size="39"
                                   maxlength="128" name="confstrs[ycCustomerId]" value="[{$customerId}]"
                                   onkeyup="yc_cutomerid_changed(this)" required />

                            <p class="oxValidateError">
                                <span class="js-oxError_notEmpty"></span>
                            </p>
                        </td>

                    </tr>
                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycLicenseKey">[{oxmultilang ident="YOOCHOOSE_CONFIG_LICENSEKEY"}]</label>
                        </td>
                        <td class="edittext">
                            <input type="text" id="ycLicenseKey" class="editinput js-oxValidate js-oxValidate_notEmpty" size="39"
                                   maxlength="128" name="confstrs[ycLicenseKey]" value="[{$licenseKey}]" required>
                            <br />
                            <span class="extra-info">[{oxmultilang ident="YOOCHOOSE_GENERAL_LICENSE_TIP1"}]
                                <a id="yoochoose-admin-link" style="color: blue;" href="https://admin.yoochoose.net/?customer_id=[{$customerId}]"
                                   target="_blank">[{oxmultilang ident="YOOCHOOSE_GENERAL_LICENSE_TIP2"}]</a></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycPluginId">[{oxmultilang ident="YOOCHOOSE_CONFIG_PLUGINID"}]</label>
                        </td>
                        <td class="edittext">
                            <input type="text" id="ycPluginId" class="editinput" size="39" maxlength="128"
                                   name="confstrs[ycPluginId]"
                                   value="[{$pluginId}]"><br />
                            <span class="extra-info">[{oxmultilang ident="YOOCHOOSE_GENERAL_PLUGINID_TIP"}]</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycEndpoint">[{oxmultilang ident="YOOCHOOSE_CONFIG_ENDPOINT" }]</label>
                        </td>
                        <td class="edittext">
                            <input class="readonly editinput" id="ycEndpoint" type="text" size="39" maxlength="128"
                                   name="confstrs[ycEndpoint]" value="[{$endpoint}]" readonly required>

                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycDesign">[{oxmultilang ident="YOOCHOOSE_CONFIG_DESIGN"}]</label>
                        </td>
                        <td class="edittext">
                            <input class="readonly editinput" id="ycDesign" type="text" size="39" maxlength="128"
                                   name="confstrs[ycDesign]" value="[{$design}]" readonly required><br />
                            <span class="extra-info">[{oxmultilang ident="YOOCHOOSE_DESIGN_TIP1"}]
                                <a style="color: blue;" href="https://doc.yoochoose.net/display/PUBDOC/OXID+Plugin+2.0+Tutorial" target="_blank">
                                    [{oxmultilang ident="YOOCHOOSE_DESIGN_TIP2"}]</a>[{oxmultilang
                                ident="YOOCHOOSE_DESIGN_TIP3"}]</span>

                        </td>
                    </tr>

                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycItemType">[{oxmultilang ident="YOOCHOOSE_CONFIG_ITEMTYPE"}]</label>
                        </td>
                        <td class="edittext">
                            <input type="number" id="ycItemType" class="editinput js-oxValidate js-oxValidate_notEmpty" size="6"
                                   min="0" name="confstrs[ycItemType]" value="[{$itemType}]" required>

                            [{foreach from=$errors key=k item=t}]
                            [{if $k=='ycItemType'}][{$t}][{/if}]
                            [{/foreach}]
                            <br />
                            <span class="extra-info">[{ oxmultilang ident="YOOCHOOSE_TYPEID_TIP1" }]
                                <a style="color: blue;" href="http://doc.yoochoose.net/" target="_blank">[{
                                    oxmultilang ident="YOOCHOOSE_TYPEID_TIP2" }]</a>.</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycLogSeverity">[{oxmultilang ident="YOOCHOOSE_CONFIG_LOG"}]</label>
                        </td>
                        <td class="edittext">
                            <select id="ycLogSeverity" name="confstrs[ycLogSeverity]">
                                [{if $logSeverity == 2}]
                                    <option value="1"> [{oxmultilang ident="YOOCHOOSE_CONFIG_LOG_INFO" }]</option>
                                    <option value="2" selected> [{oxmultilang ident="YOOCHOOSE_CONFIG_LOG_DEBUG" }]</option>
                                [{else}]
                                    <option value="1" selected> [{oxmultilang ident="YOOCHOOSE_CONFIG_LOG_INFO" }]</option>
                                    <option value="2"> [{oxmultilang ident="YOOCHOOSE_CONFIG_LOG_DEBUG" }]</option>
                                [{/if}]
                            </select>
                        </td>
                    </tr>
                    <tr></tr>

                    <tr>
                        <th class="edittext" width="180" height="40">
                            [{ oxmultilang ident="YOOCHOOSE_SCRIPT_SETTINGS_TITLE" }]
                        </th>
                    </tr>

                    <tr>

                        <td class="edittext" width="180" height="40">
                            <label for="ycPerformance">[{oxmultilang ident="YOOCHOOSE_CONFIG_PERFORMANCE"}]</label>
                        </td>

                        <td class="edittext">

                            <select id="ycPerformance" name="confstrs[ycPerformance]" class="saveinnewlanginput">
                                [{foreach from=$performanceOptions key=key item=val}]
                                    [{if $val == $performance}]
                                        <option value="[{$val}]" selected>[{oxmultilang ident=$key}]</option>
                                    [{else}]
                                        <option value="[{$val}]">[{oxmultilang ident=$key}]</option>
                                    [{/if}]
                                [{/foreach}]
                            </select>
                            <br />
                            <span class="extra-info">[{oxmultilang ident="YOOCHOOSE_PERFORMANCE_TIP"}]</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="180" height="40">
                            <label for="ycOverwrite">[{oxmultilang ident="YOOCHOOSE_CONFIG_OVERWRITE" }]</label>
                        </td>
                        <td class="edittext">
                            <input type="text" id="ycOverwrite" class="editinput" size="50" name="confstrs[ycOverwrite]"
                                   value="[{$overwrite}]">
                            [{foreach from=$errors key=k item=t}]
                            [{if $k=='ycOverwrite'}][{$t}][{/if}]
                            [{/foreach}]
                            <br />
                            <span class="extra-info"><span style="color: firebrick;">[{oxmultilang ident="YOOCHOOSE_ATTENTION"}]</span>[{oxmultilang ident="YOOCHOOSE_OVERWRITE_TIP1"}]
                                <a style="color: blue;" href="https://doc.yoochoose.net/display/PUBDOC/OXID+Plugin+2.0+Tutorial" target="_blank">
                                    [{oxmultilang ident="YOOCHOOSE_OVERWRITE_TIP2"}]</a>[{oxmultilang
                                    ident="YOOCHOOSE_OVERWRITE_TIP3"}]</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="edittext" width="180" height="40">
                        </td>
                        <td class="edittext">
                            <input type="submit" class="edittext" style="width: 210px;" name="save"
                                   value='[{oxmultilang ident="YOOCHOOSE_CONFIG_SAVE"}]' >
                        </td>
                    </tr>
                </table>

        </td>

    </tr>
</table>

