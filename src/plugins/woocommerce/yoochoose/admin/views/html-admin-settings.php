<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e('Yoochoose Settings', 'yoochoose'); ?></h2>
    <form id="mainform" method="POST">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label><?php _e('If you don\'t have a Customer ID yet, please ', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input id="yc_registerButton" type="button" class="button button-link" value="<?php _e('click here', 'yoochoose'); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        <h3>General Options</h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="customerId"><?php _e('Customer ID', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="customerId" name="customerId" value="<?= $customerId; ?>" size="50"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="licenceKey"><?php _e('Licence Key', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="licenceKey" name="licenceKey" value="<?= $licenceKey; ?>" size="50"/>
                        <p class="description">You can find your license key and detailed statistics on the <a href="https://admin.yoochoose.net" target="_blank">Yoochoose Configuration Backend</a>.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="useCountryCode"><?php _e('Use country code with language', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="useCountryCode" name="useCountryCode" <?php echo $useCountryCode ? 'checked' : ''; ?> />
                        <p class="description">Example: en_US</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="tackingScript"><?php _e('Script location', 'yoochoose'); ?></label>
                    </th>
                    <td>
                        <input type="text" placeholder="http://yoochoose.net/tracking.js" value="<?= $trackingScript; ?>" id="tackingScript" name="tackingScript" size="50"/>
                        <p class="description"><?php _e('URL of tracking script.', 'yoochoose'); ?></p>
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
    }());
</script>

