<h6 id="add">
Adding a Custom Field</h6>
<p>Home...Custom Field.</p>

<p>When adding custom fields, it is recommended to only use alpha and numeric characters for the label name. Once a
custom field has been added to an object, eg. client, quote, payment, the custom field will display at the bottom of the form for that
object in the custom field section eg. A custom field created for the client entity will appear on every client form which can be optionally used. </p>

<p>A custom field added using invoice, or quote will appear at the bottom of the invoice or quote view in the custom field section of this view. Notably no form is used here. This occurs because the first choice in the dropdown,
ie. custom field, is chosen. If any other option is chosen eg. properties, the field will appear under Address section on the quote's view, and
ultimate pdf.</p>

<p>A custom field added using client, but without choosing the custom field option, say the address option, will make sure the custom field
will appear under the address section on the client form. Not the address section on the quote.</p>

<h6 id="add-to-template">
Adding Custom Fields to an Email Template:</h6> 
<p>
Home...Setting...Email Template...Choose Invoice or Quote.
To put the custom field on an invoice, drag the added custom field onto the invoice template. 
</p>

<h6>How Custom_field table relates to other tables</h6>
<p>
<code>quote_custom</code> table has a unique id and two other ids. One points to Custom Field and the other points to the quote.
<code>quote_custom</code> stores the actual value of the field and can be variable in nature depending on 
whether the <code>custom_field</code> it is linked to is BOOLEAN, TEXT, MULTIPLE-CHOICE etc.
</p>
<p>
    Provided the $quote_custom variable on the view has been passed from the controller parameter array as eg. $parameter = ['quote_custom' => $quote_custom] <br>
    <code>$quote_custom->getQuote()->getClient_id()</code> 			will retrieve the client_id<br>
    <code>$quote_custom->getCustom_field->getLocation()</code>	will retrieve the position that the field will appear under.<br>
    <code>$quote_custom->getValue()</code>   			stores the actual value for this clients's quote.<br>
</p>
<p>
<code>custom_field</code> table stores the field build parameters and under which section  the field will appear on a form.
The custom field, for example, can appear under one of five different locations on the Client form. It will normally 
be filled in the custom field section but if not selected will appear in the other section chosen. This is determined by the location integer.
</p>
<p>
<code>custom_value</code> table extends the Custom Field Table for Field Types that require a dropdown or alternative values eg. boolean, 
and multiple choice.
</p>
<p>
The <code>*_custom</code> tables hold all the custom_field's values that belong to the * table eg. the client_custom table holds all values of the custom_field designed 
for the client form. 
</p>