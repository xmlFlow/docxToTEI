## Tokens used in xml and possible pseudocodes in Word

### Guidelines

 * Following tag symbols are reserved `#` `@` `{ }` and `=`
* `#` begins and end a markup
* `@` begins an attribute, attributes contain only _lowercase_ latin characters
* `{}` contains xml tag content
* `=` if the attribute is unclear from the order, additional attributes can be entered with @attribute=value
* tag names are in _lowercase_. Only #SB and #SE is in _uppercase_.
* If reserved symbols are necessary for the text, they are escaped by a `\` e.g. `\@` 
* `@#` is used, if a certain attribute begins with `#` 
* Order of the arttributes is important and is converted from left to right.
* For tags  lower-case case will be validated. 
* Content for XML is written in {}. 
* _Italic_ is supported. **Bold** is not supported. 
 
 ### Testing Round 3

| Nr. Status | Fehler | Remarks|
 | ---- | ---- | ---- |
| 1 :robot: |  ` व- सीis now converted to: <w>व</w> <lb break="no" n="2"/> <w>सी</w> but it have to be: <w>व<lb break="no" n="2"/>सी</w>` | New specific rule written.  | 
| 2  :robot:|  `<add place="above_the_addline" hand="first"> <w>सल्याना</w> </add>` | `New specific rule written, will work for this structure #&{जीवराज}#राज This is a little complex rule, which deviates from our generic rule  of #content# ` |
| 3   :robot: | `reverted the default value of the s element` |
| 4  :robot: |  `#cor{पु#del@overstrike{ण्य}#ण्येक}{पुण्य}#` |  `Added supporting  recursive pattern matching.  This feature has  to be tested. ` |
| 5  :robot: |  ` The attribute values within pagebreaks <pb> are wrongly placed: <pb n="#surface3" facs="2r"/> should be <pb n="2r " facs="#surface3"/>` | Corrected |
| 6  :robot: | `In the heading “Abstract”, one single word in italics was divided into two <foreign>s.guṭha became <foreign>g</foreign><foreign>uṭha</foreign>` | Corrected | 
| 7   | `When there are multiple names inside a sentence, the first name after the <s> tag do not seem to be processed: <s xml:lang="nep"><w>आगे</w> #pen{<w>रामहरी</w>}# <placeName><w>पाटनका</w></placeName>`||
| 8   | `#ref@https://web.de{गूठका खेत वढाउदै}` |  `Not yet implemented. A proper mechanism to handle urls, without breaking the existing attribute handling`|
| 9. :robot: | `[Field name in word template:] Copyright statement: --> for the <p></p> under  <availability status="restricted">.` | added |
| 10. :robot: | ` <date>yyyy</date>` | |
  




 ### Testing Round 2
  | Nr. Status | Fehler | Remarks|
 | ---- | ---- | ---- |
 | 1. :robot:  | `<space› please check again, e.g. #.....# is now converted to <space quantity="3"/> (value should be 5, @unit is missing)` |   unit default added. e.g. #.....@lines#  |
 | 2. :robot: | `I realized that no <w>s are annotated inside <persName>`| I explicitly removed it from all the places, names, and geographical locations. I reverted the function now.|
 | 3. :robot: | `<lb> is  found in all <ab>s now. Good! But still there is no <lb> for the second, third etc. line (also "-" is not working yet)`| newly implemented |
 | 4. :robot: | `ZWJ is properly integrated but ZWNJ is still outside the <w> token ` |  Had a typo| 
 | 5. :robot: | `add:  @xml:lang for <s>  [The input would be e.g., @san, @nep]` |   Implemented. defaulted to nep. Any language with three codes will be recognized. Please check this, cause I did not see this in example|
 | 6. :robot: | `not required: @reason for <surplus>` | default removed|
 | 7. :robot: | `missing: <lb @n> for each line and each <ab>` | lb should have worked, ab numbering added. Each ab begins with a n=1 or should they be incrementally counted?|
 | 8. :robot: | `not implemented: "-" at the end of line as <lb break="no">` |  newly implemented e.g handling. का- or रुक्का-#SE |
| 9. :robot: |  `not implemented: zones according to the <ab>s` | Newly implemented |
| 10.  :robot: |  `number of dots (...) for space was not correctly counted` | Corrected | 
| 11.  :robot: |   `in the footnotes, texts in italics are not transformed into <foreign>`| Added|
  
 
 
 ### Testing Round 1
 | Nr:Nutzer:Status | Fehler | Grund| Korrektur |
| ---- | ---- | ---- | ---- |
| 1. Testen | `<teiHeader> <fileDesc> <titleStmt> <title type="main">....</title> <title type="short">....</title> <title type="sub">...</title>`|Title wurde automatisch entfernt | Struktur angepasst |
| 2. Testen | `<ab type="margin" corresp="#margin1"> <lb/> <p><w>९४</w> <w>९४</w>• </p> </ab>`| paragraph elementen wurde nicht entfernt. `<lb>` nummerierung war nicht implementiert | Struktur angepasst |
| 3. Testen | `<choice><orig>....</orig><corr>---</corr></choice> ` | Hatte nur in Spezifikation geändert| Korrigiert |
| 4. Testen | `<persName><w>प्रमांनगी</w> <w>प्रमांनगी</w> • <w>प्रमांनगी</w> • <w>प्रमांनगी</w></persName> ` | Falsche Annahme: dass jedes Devanagari-Wort mit `w` eingeschlossen sein sollte | Korrigiert |
| 5. Testen | `<space> works but the @quantity is miscounted, e.g. #...# should be <space quantity="3" unit="chars"/> not <space quantity="5"... /> (does the tool also counts the #s?), furthermore: value for @unit cannot be changed (I tried #@unit=line...# but it was not converted, #...@unit=line# was converted into <space> but value is still "chars", the same with #...@@unit=line# ` | | Korrigiert. |
| 6. Testen | `</msIdentifier> nach </altIdentifier>, nicht vor <altIdentifier type="">` | | | 
| 7. Testen | `<p>For details see <ref target="...">entry in database</ref></p> URL in "..."; entry in database bleibt stehen, nicht URL statt „entry in database“.` | | | 
| 8. Testen | `<physDesc> statt <phsyDesc>` | | | 
| 9. Testen | `<persName>s works in the edition and the translation but not in the Commentary. the same holds true for <placeName>s` | | | 
| 10. Testen | `</teiHeader> nach </revisionDesc>, nicht nach </facsimile>` | | | 
| 11. Testen | `</fileDesc> vor <encodingDesc>, nicht nach </titleStmt>` | | | 
| 12. Testen | `<sourceDesc> vor <msDesc>, nicht <sourceDesc/> vor <msDesc>` | | | 
| 13. Testen | `</sourceDesc> nach </msDesc>` | | | 
| 14. Testen | `#pen{Lokaramaṇa Upādhyāya}# --> Im Commentary scheinen keinerlei tags möglich zu sein.` | | |
| 15. Testen | `<add place="place" hand="hand"> <w>नं</w> --> Die Default-values scheinen noch nicht implementiert zu sein.` | |#&{नं ९७}# sollte ` <add place="above_the_line" hand="first"><w>नं</w> <w>९७</w></add>` ausgeben|
| 16. Testen | `<lb>, so far only at the beginning of first <ab> <lb n="1"/> is included, but no <lb n="2"/> etc., furthermore <lb>s are missing in all following <ab>s (pleas note, that in every <ab> the counting should start with n="1" anew)` | | | 
| 17. Testen | `<add> works with default values, also @@second in #&, great!, also more than 1 word in an addition is correctly annotated, but additions can also occur inside a word, e.g. #&{सल्याना}#का shouldn't be <add place="above_the_line" hand="first"> <w>सल्याना</w> </add> <w>का</w> but <w><add place="above_the_line" hand="first">सल्याना</add>का</w>` | | | 
| 18. Testen | ` ZWJ (&#8205;) and ZWNJ (&#x200c;) should come inside the <w>, e.g. सर्&#8205;याको shouldn't be <w>सर्</w> &#8205; <w>याको</w> but <w>सर्&#8205;याको</w>` | Manik, I did the & conversion, but the XML was invalid. I can do it, but let us have a discussion there. | | 
| 19. Testen | `<foreign>sāhaba</foreign> <foreign> </foreign> <foreign>sikriṭari</foreign> --> Wenn ein Leerzeichen kursiviert wird, erscheint es als foreign tag. Kann man da etwas dagegen machen? Ich nehme an, dass das ein häufiger Nutzerfehler sein könnte.` | | habe ein neuer Regel hinzugefügt, falls ein tag ohne inhalt ist, sollte es entfernen|
| 20. Testen| `<surplus> works, but default @reason="repeated" is not needed; @all: or?` | | Spezifikation unischer| 
|21. Testen | last minus of a sentence| | | | <lb breank="no"> Es kann sein, dass es noch nicht nummeriert wird |




 
 
 ### Formal annotations
  
 | Status | Markup | Default | Markup Example | TEI Example | Remarks |
 | ---- | ---- | ---- | ---- | ---- | ---- |
 | 1. :heavy_check_mark:  | #SB Content #SE | | `#SBA long affairSE#` | `<s>A long affair</s>` | [s-unit](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-s.html) |
 | 2. :heavy_check_mark: | Empty line | | `` | `<lb>` | [lb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) | 
 | 3. :heavy_check_mark: | `-` | | `-` | `<lb @break=no>` | [lb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) |
 | 4. :heavy_check_mark: | #++++@extent@agent# |`extent=characters` | `#++++@agent#` | `<gap @reason=“illegible“ extent=“4 lines“> ` | [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
 | 5. :heavy_check_mark: | #///@extent@agent# |`extent=characters` | `#///@characters#` | `<gap @reason=“lost extent=“3 characters“> ` | [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
 | 6. :heavy_check_mark: | #...# | default chars | `#...@lines#` | `<space quantity="3" unit="chars"/>` | [space](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-space.html) |
 | 7. :heavy_check_mark: | #&@place@hand{}# |`place="above_the_line" hand="first"` e.g. @@second" |`#&@above the line@first#` | `<add place="above the line" hand="first"/>` | [add](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-add.html) |
 | 8. :heavy_check_mark: | #?@cert{text}# | @cert=high| `#?@high{text unclear}#` | `<unclear @cert=high> </unclear>` | [unclear](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-unclear.html) |
 | 9. :heavy_check_mark: | #cor{text}{text}# | | `#cor{Talel}{Table}#` | `<choice><sic>Tabel</sic> <corr>Table</corr></choice>` | : [sic](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-sic.html) [corr](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-corr.html) |
 | 10. :heavy_check_mark: | #orig{text}# | | `#orig{Tall}#` | `<orig>Tall</orig>` | [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html)|
 | 11. :heavy_check_mark: | #reg{text}{}# | | `#reg{Talel}{Table}#` | `<choice><orig>Tabel</orig> <reg>Table</reg></choice>` | [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html) [reg](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-reg.html) |
 | 12. :heavy_check_mark: | #sur{text}# | | `#sur@repeated{unnecessary text}#` | `<surplus reason="repeated">unnecessary text</surplus>` | [surplus](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-surplus.html) |
 | 13. :heavy_check_mark: | #sup@reason{text}# | @reason=lost| `#sup@lost{text supplied by editor}#` | `<supplied @reason=“lost>text supplied by editor </supplied>` | : [supplied](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-supplied.html) |
 | 14. :heavy_check_mark: | #del@rend{text}# | | `#DEL@rend:crossed_out{deleted text}#` | `<del @rend="overstrike">"deleted text"></del>` |  [deletion](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-del.html) |
 | 15. :heavy_check_mark: | #pen{url}# | `#pen{ Text }#` | `<persName>Text</persName>` | :ok: | [persName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-persName.html) |
 | 16. :heavy_check_mark: | #pln{url}# | `#pln{Text}#` | `<placeName>Text</placeName>` |:ok: | [placeName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-placeName.html) |
 | 17. :heavy_check_mark: |  #gen{url}# | `#gen{Text}#` | `<geogName>Text</geogName>` | :ok: | [geogName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-geogName.html) |
 | 18. :heavy_check_mark:| Word1 word2 | | `# Buddhist lirerature. #` | `<w>Buddhist </w><w>lirerature.</w>` |  [Word](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-w.html) |
 | 19. :heavy_check_mark: | `&#x200c;` | | `भो#orig{•}##-#ग्&#8205;य` | `<w>ग्&#8205;य</w> | Correct the &amp; as & |
 | 20. :heavy_check_mark: | `&#8205;` | | `भो#orig{•}##-#ग्&#x200c;य` | `<w>भो<orig>•</orig><lb n="15" break="no"/>ग्&#x200c;य</w>` | |
 | 21. :heavy_check_mark: | #div@id@type@lang# | | `#div@abs@abstract@eng#` `#div@ed@edition@nep-san@` | `<div xml:id="abs" type="abstract" xml:lang="eng">` |  [div](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-div.html)|
 | 22. :heavy_check_mark: | #ab@type@correspond# | | `#ab@addition@#addition1#` | `<ab type="addition" corresp="# addition1">` |  [Annonymous ](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-ab.html)|
 | 23. :heavy_check_mark: | #pb@p:page-number@facs#| | `#pb@#surface1@1r#` | `<pb n="1r" facs="#surface1"/>` | [pb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-pb.html) |
 | 26. :heavy_check_mark: | `.` | | . | `<orig>.</orig>` | [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html) |
 | 24. :heavy_check_mark: | between 2 `ab` s there should be `p` s| | | | |
 | 24. :heavy_check_mark: | `#ref@URL{}# `| |`#ref{URL}# e.g.  #ref{https://google.com}# #ref@URL{}# --> default attribute: target e.g ref@https://web.de{गूठका खेत वढाउदै}` | | |
 
 
 
 

 
 
 
 ### Structural identifications

| status| Issue|
|--- | ---|
| :heavy_check_mark: | Tei Header  Check: sections available ["Document metadata","Facsimiles","Abstract","Edition","English Translation","Translation","Synopsis","Commentary"] |
| :heavy_check_mark: | Tei Header  Check: header metadata [ "Alternative manifestation/inventory", "Author/issuer of document", "Date of origin of document", "Document ID", "Holding institution", "Inventory number", "Inventory number assigned by holding institution", "Link to catalogue entry", "Location ", "Main language of document", "Main title of document", "Name of editor(s)", "Name of collaborator(s)", "Name of image file(s)", "Other languages", "Place of deposit / current location of document", "Place of origin of document", "Type of manifestation ", "Holding institution" ] |
| :heavy_check_mark: | Tei Header  ( Document ID)|
| :heavy_check_mark: | Tei Header  ( Main title of document) | 
| :heavy_check_mark: | Tei Header  ( Short title of document)| 
| :heavy_check_mark: | Tei Header ( Sub of document) | 
| :heavy_check_mark: | Tei Header  ( Author/issuer of document)|
| :heavy_check_mark: | Tei Header  ( Name of editor(s)|
| :heavy_check_mark: | Tei Header  ( Name of collaborator(s))|
| :heavy_check_mark: | Tei Header  ( uestion: Changed the name to be unique in the table / ( Deposit holding institution)|
| :heavy_check_mark: | Tei Header  (Place of deposit / current location of document)|
| :heavy_check_mark: | Tei Header  (Deposit holding institution )|
| :heavy_check_mark: | Tei Header  (Inventory number assigned by holding institution )|
| :heavy_check_mark: | Can we change it to be more precise ? Tei Header  (Location )|
| :heavy_check_mark: | Tei Header  (Alternative manifestation(/inventory Type of manifestation) |
| :heavy_check_mark: | Can we change it to be more precise ? Tei Header  (Inventory number) |
| :heavy_check_mark: | Can we change it to be more precise ? Tei Header  (Holding institution) |
| :heavy_check_mark: | Tei Header  (Main language of document) |
| :heavy_check_mark: | Tei Header  (Other languages) |
| :heavy_check_mark: | Tei Header  `ref target (Link to catalogue entry) |
| :heavy_check_mark: | Tei Header  (Date of origin of document) |
| :heavy_check_mark: | Tei Header  (Place of origin of document) |
| :heavy_check_mark: | Is it needed now Tei Header  (Name of image file(s)) |
|:question: | Word: header metadata  block user edits of the header| 
| :heavy_check_mark: | Tei Header  (encoding description), default text |
| :heavy_check_mark: | Tei Header  (profile description), default text |
| :heavy_check_mark: | #AUTO for the conveterting user. Tei Header  (revision description), default text |
| :heavy_check_mark: | Validation  Error message, if metadata values aren't set |
| :heavy_check_mark: | Facsimile  (Create surfaces)|
| :heavy_check_mark: | Abstract  (Create Abstract )|
| :heavy_check_mark: |Table support|
| :heavy_check_mark: | Body (create Body element)|
| :heavy_check_mark: | Body (Check for languages in Edition (lang) )|
| :heavy_check_mark: | Body (pb) for surface|
| :heavy_check_mark: | Body (ab) for surface|
| :heavy_check_mark: | Change header according to synopsis and et. ``` <respStmt> <resp>main editor and translator</resp> <name type="main_editor">Max Mustermann</name> </respStmt>  <respStmt><resp>main editor</resp> <name type="synopsis_editor">Max Mustermann</name> </respStmt>```  | 
| :heavy_check_mark: | Body (ab) for surface|
| :heavy_check_mark: | Numbering|

 

* `<ab>` : #AB{}# is only for edition block and :question: abstract Rest blocks will have a `<p>` 
* Add _iso_ language list and add it to define the default language { nep, san, new, hin, tib, eng}
* New attributes can be introduced using @newAtrtirb=newValue
* User errors in tags e.g. `#abc {}` should be captured. 
* double-spaces inside text should be corrected.
* page beginning is always the first element for Edition and translation begins `<pb n="1r"/>`
* Lines should be numbered beginning from n=1 for each anonymous block.
* person,place,geog has no attribute n is not needed.
* create error log in HTML
* add hand and place are mandatory and will be defaulted by first and above the line. Other examples `<add hand="second" place="in the upper margin">नं.६६</add>`
* footnotes are created inside note.
* `italic` in english has to converted to `foreign`
* ignore term ref and biblio comes later.
* Each word is wrapped with `<word></word>`
* Either translation or synopsis is used. A rule has to be written.
* Joiner and non-joiners will be added directly into the templates: [Zero Width Non-Joiner &#x200c;](https://www.codetable.net/hex/8204) , [Zero Width Joiner &#x200d;](https://www.codetable.net/decimal/8205)  
* Tables are held simple for the formatting.
* Check for macros to auto-complete.


 
 
 

 ### Discussion
 

> `<add>` I propose place="above the line" hand="first" as default. @Dulip: Can we do "@@second" to get place="above the line" hand="second" or would this result in place="" hand="second"? (below in the list the entry "#&&@place:place content#" .... can be deleted)

 :heavy_check_mark: 

> -<pb>: I think we can simplify it: #pb@n@facs# and take as default for facs "surface1" (most of the document have only one page). Thus "#pb@1r" would be <pb n="1r" facs="#surface1"/>.
[[@all: should we also define "1r" as default for @n? Thus if we type "#pb" we get <pb n="1r" facs="#surface1"/>?]]

:heavy_check_mark: When I talked with Manik we also added a proposal to the zones under section Facsimiles in [google doc](https://docs.google.com/document/d/1vUsRn0wUryExGf8AOFbvqUCT9XFHZ6lWN3lNeouKSEU/edit#heading=h.2q9tj211wfsd).
 
> `<s>` how to mark the end of a sentence "SE#" or "#SE"? For #SB we would also need in some cases @xml:lang but the default option should be that there is no @.

:heavy_check_mark: Changing the default place of # the style will be a little tricky and can be error phone. What about that we support both #SB my sentence SE#. We can #SB{} for special cases.

> --for "edition": <div xml:id="ed" type="edition" xml:lang="nep-san">; @Dulip: since the language can be different here (e.g. new-san) could be have a field for "language" beside "edition" in the template whose content would overwrite the default value in xml:lang?

:heavy_check_mark: What about we write the Langauge in brackets for non-default ones ? Edition (isocode of other-language) ?

> What are the special characteristics of tables


> `<orig>/<reg>` in `<choice>`, maybe better #reg instead of #orig? @Dulip please change in "Tei example" "corr" to "reg". For for <orig> alone: #orig{}? @Dulip, we mark e.g. the nukta sign ( ़) used inside words as <orig>. If this is done by the editors in the word edition its nasty. Could your tool automatically replace all " ़" in the edition by "<orig> ़</orig>"? That would be a great help!

:heavy_check_mark: I have changed `orig` to `reg`.  I added a new line to replace `.` with a `<orig>.</orig>` 

> in <gap>: shouldn't it be ....@agent instead of ...#agent? As default value for extent we can use "characters", the number is taken from the amount of +-s or /-s.
 
:heavy_check_mark: I have corrected the mistake and took characters as default. 
 
> `<unclear>` default value for @cert should be "high"

:heavy_check_mark:

> `<sic>/<corr> `in `<choice>`: can we use "#cor" instead of "#crt" 

:heavy_check_mark:


> surplus: Maybe better "#sur" instead of "#srp"

:heavy_check_mark:

> `<ab> `could the tool add `<p/>` after `</ab>` (Manik needs it for the stylesheet)

:heavy_check_mark: Sure, I have added i a todo for me. 

> div: as discussed, we use a field or a heading in the template. default values are:
 --for "abstract": <div xml:id="abs" type="abstract" xml:lang="eng">

:heavy_check_mark:
 
> --for "translation": `<div xml:id="et" type="english_translation" corresp="#ed" xml:lang="eng">`

:heavy_check_mark:
 
> --or for "synopsis": `<div xml:id="syn" type="synopsis" corresp="#ed" xml:lang="eng">`

:heavy_check_mark:

> @Dulip: the file will have either a translation or a synopsis and depending on what occurs the TEI header needs to be modified (see Simon's list). In DN we simply use two different xml templates, one for edions with translation and another for editions with synopsis. Should we do the same for the word template(s)? Or can you programm that the header is automatically modified according to what div is used?]]
--for "commentary": `<div xml:id="commentary" type="commentary" xml:lang="eng">`

:heavy_check_mark: We can work with one template, I can modify the header values, depending on whether translation or synopsis available.

 
> <space>: default for @unit: "chars"

:heavy_check_mark:

> `<supplied>` maybe better "#sup" instead of "#spl". Default for @reason: lost

:heavy_check_mark:

> `<w>` @MANIK: If you have not done so yet, please send Dulip your regex
 
:heavy_check_mark:
 # install

