<h6 id="invoice">Development Summary: (...views/info/invoice.php)</h6>
<b>2nd June 2022</b>
<p>Aim: To develop a similar invoicing system to InvoicePlane integrating with the latest Jquery, and security features of Yii3 using wampserver
as a test platform.
</p>
<p><a href="https://github.com/yiisoft/demo/issues/439" >Issue 439: BelongsTo relation not updating on edit of relation field eg. Product' relation field tax rate is not editing and updating.</a> 
<p>Quote - The Quote is functional ie. can be pdf'd but the emailing aspect has to be developed.</p>
<p>Invoice - The Invoice is functional ie. can be pdf'd and archived but the emailing aspect has to be developed.</p> 
<p>Recurring invoices - Functional but not fully tested.</p>
<p>Payment - Can be recorded against an Invoice. The latest version in League/Omnipay v3.2 has been setup with a few of the major payment providers added to the composer.json</p>
<p>User Custom Fields - not started yet.</p>
<p>File Attachments - not started yet. </p>
<p>Settings...View(Debug mode ie. Red) - These are being used.</p>
<p>Settings...View(Non-Debug mode ie. Not Red) - Some are being used. Their functionality is currently being analysed in Invoiceplane.--</p>
<p>The userinv table is an extension of yii/demo's user table and contains all the critical user information.</p>
</p>
<b>Setting Up</b>
<p>The settings table builds up initially with a default array of settings via the InvoiceController if they do not exit ie. setting 'default_settings_exist' does not exist.</p>
<b>Generator</b>
<p>The code generator templates have been adapted according to the latest demo updates.</p>
<b>Annotations</b>
<p>The lengthy Entity annotations have been replaced with the more concise Attributes coding structure. eg. <code> * @ORM\Column(type="string")</code>
    replaced with <code>#[ORM\Column(type: "string")]</code>. However issue 439 is currently relevant here.  
</p>
<b>Demo Mode</b>
<p>A demo mode variable located in src\Invoice\Layout\main.php ie. <code>$demo_mode</code> can be set to false to remove performance settings and the clear cache tool.
All areas in red will be removed.</p>
<b>Jquery</b>
<p>Jquery  3.6.0 (March 2nd 2021) version is being used for custom fields, and smaller modals. Temporarily, Invoiceplane's dependencies.js file is being used in AppAsset.
The modals are dependent on it. </p>
<b>Html Tags on Views</b>
<p>Views can be improved with more Yii related tags ie. using <code>Html::tag</code>. Html::encode is mandatory or compulsory or always present.</p>
<b>Paginator</b>
<p>The length of the lists can be changed via setting/view: <code>default_list_limit</code></p>
<b>Locales</b>
<p>The SettingRepository <code>load_language_folder</code> function accepts the dropdown locale through yiisoft/demo's <code>$session->get('_language')</code> function: this setting takes precedence over the database 'default_language' setting when set.</p>
<b>Client's language different to locale_derived_language or fallback settings 'default_language'</b>
<p>When printing occurs, the client's language ensures the documentation is printed out in his/her language using <code>$session->get('print_language')</code>
<p>The session variable <code>print_language</code> is reset after printing.</p>    
<b>Languages</b>
<p>Any words used not in the Invoiceplane folders, will be translated using Yii's translation methodology.</p>
<p>The above menu's language can be created in ...resources/messages for a specific language.
<p>Language folders can be imported from Invoiceplane but the following code must be inserted in each file <code>declare(strict_types=1);</code>within that folder.</p>
<b>Steps to include a language</b>
<p>1. Include the language folder in src/Invoice/Language after including declare(strict_types) in each file.</p>
<p>2. Include the new language in SettingsRepository's locale_language_array</p>
<p>3. Adjust the config/params.php locales array.</p>
<p>4. Adjust the views/layout/main.php menu.</p>
<p>5. Adjust each of the resources/messages folders language array.</p>
<p>6. CJK (C(hinese) J(apanese) K(orean) Languages <a href="https://mpdf.github.io/fonts-languages/cjk-languages.html"></a>
In order to view a file with non-embedded CJK (chinese-japanese-korean) fonts, you - and other users - need to download the Asian font pack for Adobe Reader for the languages:

Chinese (Simplified),
Chinese (Traditional),
Korean,
and Japanese

<a href="https://helpx.adobe.com/acrobat/kb/windows-font-packs-32-bit-reader.html">For Windows</a>
<a href="https://helpx.adobe.com/acrobat/kb/macintosh-font-packs�acrobat�reader-.html">For Mac</a></p>
<p>If spaces appear where the language should appear whilst viewing using eg. Chrome default PDF reader, add the extension - Chrome PDF Viewer 2.3.164.</p>
<p>7. When copying, and pasting the Chinese Simplified folder make sure that you remove the space between the Chinese and Simplified. ie. ChineseSimplified. This is camelcase. </p>
<b>Netbeans: <a href="https://stackoverflow.com/questions/59800221/gradle-netbeans-howto-set-encoding-to-utf-8-in-editor-and-compiler">How to include UTF-8 in Netbeans</a></b>
<p>Set encoding used in Netbeans globally to UTF-8. Added in netbeans.conf "-J-Dfile.encoding=UTF-8" to parameter "netbeans_default_options". This unfortunately has
to be done everytime you edit a file with 'special letters'. So edit the file with the UTF-8 setting above, save it, and then remove the above setting from Netbeans.conf. </p> 
<b>Improved Features</b>
<p>Multiple products of quantity of 1 can be selected from the products lookup with the 'burger' or '3 horizontal lines' icon whilst in quote.</p>
<p>Multiple items can be deleted under the options button with 'Delete Item'.</p>
<p>Company details are divided into public and private. The profile table is intended for multiple profiles especially when email addresses, and mobile numbers change.</p>
<p>If a Tax -Rate is set to default, it will be used on all new quotes and invoices. ie. A Quote Tax Rate will be created from this Tax Rate automatically and will be created and used on all new quotes. The same will apply to all invoices.</p>
<p>If you want to create a quote or an invoice group, specific to a client, use the Setting...Group feature to setup a Group identifier eg. JOE for the  JOE Ltd Company. The next id will be appended to JOE ie. JOE001.</p>
<b>Deprecated Original Features</b>
<p>Themes will be introduced at a later date.</p>
<p>It is not intended to deprecate any of the features currently in InvoicePlane.</p>
<b>Proposed Features</b>
<p>An interlinked basic bookkeeping system to audit transactions.</p>
<b>Security</b>
<p>All Entity properties initialized before the construct should be private. The private property is accessed through a public getter method as built below the construct.</p>
<b>Reasons for using a simplified <code>id</code> as a primary key in all the tables</b>
<p>See <a href="https://cycle-orm.dev/docs/annotated-relations/1.x/en#belongsto">{relationName}_{outerKey}</a>, the outerKey being the primary key, structure. 
    Eg. the field <code>tax_rate_id</code> in the Product table is a relation or a foreign key in the Product table equal and pointing to its parent table's Tax Rate's <code>id</code>  
    so the relation name <b>variable</b> in Entity: Product must be <code>$tax_rate</code> and joined with the outerKey as <code>$id</code> you get <code>$tax_rate_id</code> which matches the foreign key <code>$tax_rate_id</code> in Entity: Product
    If the primary key in the Tax Rate table was named something like tax_rate_id and not id then the relation could not be given a name.
</p>
<b>Future Work</b>
<p>A new feature of product custom fields has to be developed.</p>
<p>Client Custom Fields dependency on client_custom_fields.js will be removed as has been done for Payment Custom Fields. </p>
<p>Redundant functions generated by the Generator have to be deleted.</p>
<b>Work in progress</b>
<p>All the settings in the setting view,  still have to be linked to their specific purpose by consulting with the Original Invoiceplane code.</p>
<p>User custom fields have to be developed.</p>