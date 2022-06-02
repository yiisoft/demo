<h4>Generate code using templates in templates_protected folder:</h4>

<h6 id="improvements">Improvements to the Generator - Today's date: 28 January 2022 </h6>

<p>The following code eg. <code>$id=$request->getAttribute('id')</code> was used to access the Route's (ie. config/routes.php) {id} parameter. This has changed to
to <code>$id=$currentRoute->getArgument('id')</code> with the inclusion of <code>use Yiisoft\Router\CurrentRoute;</code> in the namespace.</p> 

<p>Also, hopefully the generator will be a little more understandable with the use of a few more examples.</p>

<p>The mapper code has been improved mainly to accomodate the <code>date_modified</code> and <code>date_created</code> fields.</p>

<p>Implementing a <code>hasMany</code> relation in a parent Entity eg. Quote (has many quote_items) created duplication of the relative field <bold>in the child table</bold> through Cycle if you have a Belongsto relation
   in the child Entity eg. QuoteItem. They are mutually exclusive. ie. if you have a hasMany relation in the parent you cannot have a BelongsTo in the child. Just a word of caution,
   if you decide to use a BelongTo relation in the <code>Generator Relation (child table ie. table eg ProductCategory with foreign key eg. product_id pointing to Product Table)</code> you should not have a hasMany in the Parent table. 
</p>

