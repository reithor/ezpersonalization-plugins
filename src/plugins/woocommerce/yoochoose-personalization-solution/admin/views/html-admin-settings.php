<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<style>
    .toss-message-success{
        background-color: greenyellow;
        text-align: center;
        padding: 5px;
    }

    .toss-message-error{
        background-color: orangered;
        text-align: center;
        padding: 5px;
    }
</style>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e('Yoochoose Settings', 'yoochoose'); ?></h2>
    <div style="display: <?php echo empty($this->tossMessages) ? 'none' : 'block'; ?>">
        <?php 
        foreach ($this->tossMessages as $toss) {?>
        <h3 class="toss-message-<?= $toss['type']; ?>"><?= $toss['message']; ?></h3>
       <?php  }
        ?>
    </div>
    <form id="mainform" method="POST">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label><?php _e('If you don\'t have a Customer ID yet, please ', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input id="yc_registerButton" type="button" class="button button-link" value="<?php _e('Register new Yoochoose account', 'yoochoose'); ?>" />
                        <p class="description"><?php _e('Please visit', 'yoochoose'); ?> <a href="https://www.yoochoose.com" target="_blank"><?php _e('Yoochoose website', 'yoochoose'); ?></a> 
                            <?php _e('for information to pricing, data privacy and terms of service.', 'yoochoose'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <h3><?php _e('General Options', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="customerId"><?php _e('Customer ID', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="customerId" name="customerId" value="<?= $customerId; ?>" size="50" required/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="licenceKey"><?php _e('Licence Key', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="licenceKey" name="licenceKey" value="<?= $licenceKey; ?>" size="50" required/>
                        <p class="description"><?php _e('You can find your license key and detailed statistics on the', 'yoochoose'); ?> <a href="<?= $ycAdminLink ?>" target="_blank"><?php _e('Yoochoose Configuration Backend', 'yoochoose'); ?></a>.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="endpoint"><?php _e('Endpoint', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="endpoint" name="endpoint" value="<?= $endpoint; ?>" size="50" <?php echo !$overrideDesign ? 'readonly' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="design"><?php _e('Design', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="design" name="design" value="<?= $design; ?>" size="50" <?php echo !$overrideDesign ? 'readonly' : ''; ?>/>
                        <p class="description"><?php _e('We will try to find a design template for you shop. Please read ', 'yoochoose'); ?>
                            <a href="https://doc.yoochoose.net/display/PUBDOC/WooCommerce+Plugin+Tutorial" target="_blank"><?php _e('Yoochoose Connect Extention Tutorial', 'yoochoose'); ?></a>, 
                            <?php _e('if you need to customize the desing of the recommendations.', 'yoochoose'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc"></th>
                    <td>
                        <input type="checkbox" id="overrideDesign" name="overrideDesign" <?php echo $overrideDesign ? 'checked' : ''; ?> />
                        <p class="description"><?php _e('Overwrite endpoint and design configuration, 
                                    if exists (it happens once, as you clicks "Save")', 'yoochoose'); ?> </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="useCountryCode"><?php _e('Use country code with language', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="useCountryCode" name="useCountryCode" <?php echo $useCountryCode ? 'checked' : ''; ?> />
                        <p class="description"><?php _e('Example: en_US, change language <a href="options-general.php#WPLANG" target="_blank">here</a>.', 'yoochoose'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="useCountryCode"><?php _e('Log severity', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <select name="logSeverity" id="logSeverity">
                            <option value="1" <?php echo $logSeverity == 1 ? 'selected' : ''; ?>><?php _e('Info',
                                    'yoochoose'); ?></option>
                            <option value="2" <?php echo $logSeverity == 2 ? 'selected' : ''; ?>><?php _e('Debug',
                                    'yoochoose'); ?></option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Script Endpoint', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="cdnSource"><?php _e('Performance', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <select name="cdnSource" id="cdnSource">
                            <option value="1" <?php echo $cdnSource == 1 ? 'selected' : ''; ?>><?php _e('Load scripts directly from Yoochoose server', 'yoochoose'); ?></option>
                            <option value="2" <?php echo $cdnSource == 2 ? 'selected' : ''; ?>><?php _e('Load scripts from the Amazon content delivery network (CDN)', 'yoochoose'); ?></option>
                        </select>
                        <p class="description"><?php _e('CDN mode provide better performance but takes about a 30 minutes, if the '
                                . 'configuration is updated. Please switch to CDN only, if the configuration is done and stable.', 'yoochoose'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="scriptOverwrite"><?php _e('Overwrite Endpoint', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" placeholder="http://yoochoose.net/tracking.js" value="<?= $scriptOverwrite; ?>" id="scriptOverwrite" name="scriptOverwrite"
                               pattern="<?= trim(rtrim(self::SCRIPT_URL_REGEX, '/'), '/'); ?>" size="50"/>
                        <p class="description"><strong style="color: red;"><?php _e('Attention!', 'yoochoose'); ?></strong> 
                            <?php _e('See the', 'yoochoose'); ?> <a href="https://doc.yoochoose.net/display/PUBDOC/WooCommerce+Plugin+Tutorial" target="_blank">
                                <?php _e('extension manual', 'yoochoose'); ?></a>, <?php _e('if you about to use this property.', 'yoochoose'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Best seller block on the home page', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="renderBestseller"><?php _e('Render recommendation', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="renderBestseller" name="boxes[bestseller][render]" <?php echo $boxes['bestseller']['render'] ? 'checked' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="titleBestseller"><?php _e('Title', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="titleBestseller" name="boxes[bestseller][title]" value="<?= $boxes['bestseller']['title']; ?>" size="50" />
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Personalized recommendation block on the home page', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="renderPersonalized"><?php _e('Render recommendation', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="renderPersonalized" name="boxes[personal][render]" <?php echo $boxes['personal']['render'] ? 'checked' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="titlePersonalized"><?php _e('Title', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="titlePersonalized" name="boxes[personal][title]" value="<?= $boxes['personal']['title']; ?>" size="50" />
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Upselling block of the product details page', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="renderUpselling"><?php _e('Render recommendation', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="renderUpselling" name="boxes[upselling][render]" <?php echo $boxes['upselling']['render'] ? 'checked' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="titleUpselling"><?php _e('Title', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="titleUpselling" name="boxes[upselling][title]" value="<?= $boxes['upselling']['title']; ?>" size="50" />
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Related products block of the product details page', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="renderRelated"><?php _e('Render recommendation', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="renderRelated" name="boxes[related][render]" <?php echo $boxes['related']['render'] ? 'checked' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="titleRelated"><?php _e('Title', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="titleRelated" name="boxes[related][title]" value="<?= $boxes['related']['title']; ?>" size="50" />
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Crossselling block of the shopping basket', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="renderCrossselling"><?php _e('Render recommendation', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="renderCrossselling" name="boxes[crossselling][render]" <?php echo $boxes['crossselling']['render'] ? 'checked' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="titleCrossselling"><?php _e('Title', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="titleCrossselling" name="boxes[crossselling][title]" value="<?= $boxes['crossselling']['title']; ?>" size="50" />
                    </td>
                </tr>
            </tbody>
        </table>
        <hr/>

        <h3><?php _e('Category block of the category page', 'yoochoose'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="renderCategory"><?php _e('Render recommendation', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="renderCategory" name="boxes[category_page][render]" <?php echo $boxes['category_page']['render'] ? 'checked' : ''; ?>/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="titleCategory"><?php _e('Title', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="titleCategory" name="boxes[category_page][title]" value="<?= $boxes['category_page']['title']; ?>" size="50" />
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save changes', 'yoochoose'); ?>"></p>
    </form>
</div>

<script type="text/javascript">
    (function () {
        document.getElementById('yc_registerButton').onclick = function () {
            var form = document.createElement("form"),
                    input = document.createElement("input"),
                    object = <?php echo $registrationInfo; ?>;

            form.method = "post";
            form.action = "<?php echo $registrationLink; ?>";
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
        
        document.getElementById('overrideDesign').onclick = function () {
            var design = document.getElementById('design'),
                endpoint = document.getElementById('endpoint');
                
                design.readOnly = !design.readOnly;
                endpoint.readOnly = !endpoint.readOnly;
        };
    }());
</script>