## Tokens used in xml and possible pseudocodes in Word

### TODO
| status| Issue|
|--- | ---|
| :ok: | Tei Header   Check: sections available ["Document metadata","Facsimiles","Abstract","Edition","English Translation","Translation","Synopsis","Commentary"] |
| :ok: | Tei Header   Check: header metadata  [ "Alternative manifestation/inventory", "Author/issuer of document", "Date of origin of document", "Document ID", "Holding institution", "Inventory number", "Inventory number assigned by holding institution", "Link to catalogue entry", "Location ", "Main language of document", "Main title of document", "Name of editor(s)", "Name of collaborator(s)", "Name of image file(s)", "Other languages", "Place of deposit / current location of document", "Place of origin of document", "Type of manifestation ", "Holding institution" ] |
| :ok: | Tei Header   ( Document ID)|
| :ok: | Tei Header   ( Main title of document) | 
| :ok: | Tei Header   ( Short title of document)|  
| :ok: | Tei Header ( Sub of document) |  
| :ok: | Tei Header   ( Author/issuer of document)|
| :ok: | Tei Header   ( Name of editor(s)|
| :ok: | Tei Header   ( Name of collaborator(s))|
| :ok: | Tei Header   ( :question: Changed the name to be unique in the table / ( Deposit holding institution)|
| :ok: | Tei Header   (Place of deposit / current location of document)|
| :ok: | Tei Header   (Deposit holding institution )|
| :ok: | Tei Header   (Inventory number assigned by holding institution )|
| :ok: :question: |  Can we change it to be more precise ? Tei Header   (Location )|
| :ok: | Tei Header   (Alternative manifestation(/inventory Type of manifestation) |
| :ok: :question: |  Can we change it to be more precise ? Tei Header   (Inventory number) |
| :ok: :question: |  Can we change it to be more precise ? Tei Header   (Holding institution) |
| :ok: | Tei Header   (Main language of document) |
| :ok: | Tei Header   (Other languages) |
| :ok: | Tei Header   :question: `ref target (Link to catalogue entry) |
| :ok: | Tei Header   (Date of origin of document) |
| :ok: | Tei Header   (Place of origin of document) |
| :question: | Is it needed now ??Tei Header   (Name of image file(s)) |
|:question:  |  Word: header metadata   block user edits of the header| 
| :ok: | Tei Header   (encoding description), default text |
| :ok: | Tei Header   (profile description), default text |
| :question: | How to define the user  e.fg #AZ? Tei Header   (revision description), default text |
| :ok: | Validation   Error message, if metadata values aren't set |
| :ok: | Facsimile    (Create surfaces)|
| :ok: | Abstract    (Create  Abstract )|
| :question: | Needs an example of a table|
| :ok: | Body (create Body element)|
| :ok: | Body (Check for languages in Edition  (lang) )|
| :ok: | Body (pb) for surface|
| :ok: | Body (ab) for surface|


 

* `<ab>` : #AB{}# is only for edition block and :question:  abstract Rest blocks will have a `<p>` 
* Add _iso_ language list and  add it to define  the default language  { nep, san, new, hin, tib, eng}
* New attributes can be introduced using @newAtrtirb=newValue
* User errors in tags  e.g. `#abc {}` should be captured. 
*  double-spaces inside text should be corrected.
* page beginning is always the first  element for  Edition and translation begins  `<pb n="1r"/>`
* Lines should be numbered beginning from n=1 for each anonymous block.
* person,place,geog has no attribute n is  not needed.
* create error log in HTML
* add hand and place are mandatory and will be defaulted by first and above the line. Other examples `<add hand="second" place="in the upper margin">नं.६६</add>`
* footnotes are created inside note.
* `italic` in english has to converted to `foreign`
* ignore term ref and biblio comes later.
* Each word is wrapped with `<word></word>`
* Either translation or synopsis is used. A rule  has to be written.
* Joiner and non-joiners will be added  directly into the templates: [Zero Width Non-Joiner &#x200c;](https://www.codetable.net/hex/8204) ,  [Zero Width Joiner &#x200d;](https://www.codetable.net/decimal/8205)   
* Tables are held simple for the formatting.
* Check for macros to auto-complete.


### Guidelines

 *  Following  tag symbols. `#` `@` `{ }` and `:`
       * `#` begins and end a markup
       * `@` begins an attribute
       * `{}` contains xml tag content
       * `:`  if the attribute is unclear from the order,we use : after attribute name  @attribute:value  
 * If reserved  symbols are necessary for the text, they  are escaped by a `\` e.g. `\@` 
 * `@#` is  used, if a certain attribute begin with `#` 
 * For markup upper-small case will be validated. 
 * Order of the arttributes is important and will be read from let to right.
 * Content for XML or attributes is written in {}, always at the end of the markup. 
 * Add `<p/>` after each `<ab>`
 * Abstract defaults to `<div xml:id="abs" type="abstract" xml:lang="eng">`
 * Edition defaults to `<div xml:id="ed" type="edition" xml:lang="nep-san">`
 
 ### Formal annotations (div, ab, pb, lb, w, s, space, table, row, cell, add, del, gap, unclear)
  
 | S | Markup | Default | Markup Example | TEI Example | Remarks |
 | ---- | ---- | ---- | ---- | ---- |  ---- |
 | :ok: | &#x200c; | | `भो#orig{•}##-#ग्&#8205;य` | `<w>भो<orig>•</orig><lb n="15" break="no"/>ग्&#8205;य</w> |  |
 | :ok: | &#8205; | | `भो#orig{•}##-#ग्&#x200c;य` | `<w>भो<orig>•</orig><lb n="15" break="no"/>ग्&#x200c;य</w>` |  |
 | :ok: | #del@rend{text}# | | `#DEL@rend:overstrike{deleted text}#` | `<del @rend="overstrike">"deleted text"></del>` |   [deletion](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-del.html) |
 | :ok: | #SB Content #SE | | `#SB{A short affair}#` | `<s>A short affair</s>` |  [s-unit](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-s.html) |
 | :ok: | #ab@type@correspond# |  | `#@ab@addition@#addition1#` | `<ab type="addition" corresp="# addition1">` |   [Annonymous ](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-ab.html)|
 | :ok: | #@place&@hand{}# |`place="above the line" hand="first"` e.g. @@second" |`#&@above the line@first#` | `<add place="above the line" hand="first"/>` | [add](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-add.html) |
 | :ok: | #...# |@unit=chars | `#...#` | `<space quantity="3" unit="chars"/>` |  [space](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-space.html) |
 | :ok: | #div@id@type@lang# | | `#div@abs@abstract@eng#` `#div@ed@edition@nep-san@` | `<div xml:id="abs" type="abstract" xml:lang="eng">` |   [div](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-div.html)|
 | :ok: | #pb@p:page-number@facs#|  | `#@pb@p=12@#surface1#` | `<pb n="1r" facs="#surface1"/>` |  [pb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-pb.html) |
 | :ok: | #///@extent@agent#  |`extent=characters` | `#///@characters#` | `<gap @reason=“lost extent=“3 characters“> ` | [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
 | :ok: | #++++@extent@agent# |`extent=characters` | `#++++@agent#` | `<gap @reason=“illegible“ extent=“4 lines“> `  | [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
 | :ok: | `.` |  | . | `<orig>.</orig>`  | [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html) |
 | :ok: | Word1 word2 | |  `#  Buddhist lirerature. #` | `<w>Buddhist </w><w>lirerature.</w>` |   [Word](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-w.html) |
 | :ok: | `-` |  | `-` | `<lb @break=no>`  | [lb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) |
 | :ok: | Empty line | | `` | `<lb>` | [lb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html)  | 
 | :ok: | #?@cert{text}# | @cert=high| `#?@high{text unclear}#` | `<unclear @cert=high> </unclear>` |  [unclear](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-unclear.html) |
 | :ok: | #orig{text}# | | `#orig{Tall}#` | `<orig>Tall</orig>`  |  [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html)|
 | :ok: | #reg{text}{}# | | `#reg{Talel}{Table}#` | `<choice><orig>Tabel</orig> <corr>Table</corr></choice>`  |  [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html) [reg](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-reg.html) |
 | :ok: | #cor{text}{text}# | |  `#cor{Talel}{Table}#` | `<choice><sic>Tabel</sic> <corr>Table</corr></choice>` | :  [sic](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-sic.html) [corr](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-corr.html) |
 | :ok: | #sur{text}# | |  `#sur@repeated{unnecessary text}#` | `<surplus reason="repeated">unnecessary text</surplus>` | [surplus](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-surplus.html) |
 | :ok: | #sup@reason{text}# | @reason=lost| `#spl@lost{text supplied by editor}#` | `<supplied @reason=“lost>text supplied by editor </supplied>` | : [supplied](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-supplied.html) |
 
 
 ### Content annotation (persName, placeName, geogName)
 | Markup  | Markup Example | TEI Example | Status|  Remarks |
 | --- | ---- | --- | --- | --- |
 | #pen{url}# | `#pn{corresp_ID}#` | `<persName corresp="corresp_ID"/>` | :ok: | [persName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-persName.html) |
 | #pln{url}# | `#pln{corresp_ID}#` | `<placeName  corresp="corresp_ID"/>` |:ok: | [placeName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-placeName.html) |
 | #gen{url}# | `#gen{corresp_ID}#` | `<geogName corresp="corresp_ID"/>` | :ok: |  [geogName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-geogName.html) |
 

 

 ### Discussion
 

> `<add>` I propose place="above the line" hand="first" as default. @Dulip: Can we do "@@second" to get place="above the line" hand="second" or would this result in place="" hand="second"? (below in the list the entry "#&&@place:place content#" .... can be deleted)

 :heavy_check_mark: 

> -<pb>: I think we can simplify it: #pb@n@facs# and take as default for facs "surface1" (most of the document have only one page). Thus "#pb@1r" would be <pb n="1r" facs="#surface1"/>.
[[@all: should we also define "1r" as default for @n? Thus if we type "#pb" we get <pb n="1r" facs="#surface1"/>?]]

:heavy_check_mark:  When I talked with Manik we also added a proposal to the zones under section Facsimiles  in [google doc](https://docs.google.com/document/d/1vUsRn0wUryExGf8AOFbvqUCT9XFHZ6lWN3lNeouKSEU/edit#heading=h.2q9tj211wfsd).
  
> `<s>` how to mark the end of a sentence "SE#" or "#SE"? For #SB we would also need in some cases @xml:lang but the default option should be that there is no @.

:heavy_check_mark:  Changing the default place of # the  style will be a little tricky and can be error phone. What about that we support both #SB my sentence SE#.  We can  #SB{} for special cases.

>  --for "edition": <div xml:id="ed" type="edition" xml:lang="nep-san">; @Dulip: since the language can be different here (e.g. new-san) could be have a field for "language" beside "edition" in the template whose content would overwrite the default value in xml:lang?

:heavy_check_mark:  What  about we write the Langauge in brackets for non-default ones ? Edition (isocode of other-language)  ?

> What are the special  characteristics of tables


> `<orig>/<reg>` in `<choice>`, maybe better #reg instead of #orig? @Dulip please change in "Tei example" "corr" to "reg". For for <orig> alone: #orig{}? @Dulip, we mark e.g. the nukta sign ( ़) used inside words as <orig>. If this is done by the editors in the word edition its nasty. Could your tool automatically replace all " ़" in the edition by "<orig> ़</orig>"? That would be a great help!

:heavy_check_mark: I have changed `orig` to `reg`.   I added a new line to replace `.` with a `<orig>.</orig>`  

> in <gap>: shouldn't it be ....@agent instead of ...#agent? As default value for extent we can use "characters", the number is taken from the amount of +-s or /-s.
 
:heavy_check_mark: I have corrected the mistake and took characters as default. 
  
> `<unclear>` default value for @cert should be "high"

:heavy_check_mark:

> `<sic>/<corr> `in `<choice>`: can we use "#cor" instead of "#crt" 

:heavy_check_mark:


> surplus: Maybe better "#sur" instead of "#srp"

:heavy_check_mark:

> `<ab> `could the tool add `<p/>` after `</ab>` (Manik needs it for the stylesheet)

:heavy_check_mark: Sure, I have added i  a todo for me. 

> div: as discussed, we use a field or a heading in the template. default values are:
  --for "abstract": <div xml:id="abs" type="abstract" xml:lang="eng">

:heavy_check_mark:
 
>  --for "translation": `<div xml:id="et" type="english_translation" corresp="#ed" xml:lang="eng">`

:heavy_check_mark:
  
> --or for "synopsis": `<div xml:id="syn" type="synopsis" corresp="#ed" xml:lang="eng">`

:heavy_check_mark:

> @Dulip: the file will have either a translation or a synopsis and depending on what occurs the TEI header needs to be modified (see Simon's list). In DN we simply use two different xml templates, one for edions with translation and another for editions with synopsis. Should we do the same for the word template(s)? Or can you programm that the header is automatically modified according to what div is used?]]
--for "commentary": `<div xml:id="commentary" type="commentary" xml:lang="eng">`

:heavy_check_mark:  We can work with one template, I can modify the header values, depending on whether translation or synopsis available.

 
> <space>: default for @unit: "chars"

:heavy_check_mark:

> `<supplied>` maybe better "#sup" instead of "#spl". Default for @reason: lost

:heavy_check_mark:

> `<w>` @MANIK: If you have not done so yet, please send Dulip your regex
 
:heavy_check_mark:
 
    
 
### Not to foreget
| S | Markup | Default | Markup Example | TEI Example | Remarks |
| ---- | ---- | ---- | ---- | ---- |  ---- |
| :question: | #sp{@extenty@unit}# |@unit=chars | `#sp{@3@lines}` | `<space quantity="3" unit="chars"/>` |  [space](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-space.html) |
