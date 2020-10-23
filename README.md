## Tokens used in xml and possible pseudocodes in Word

### Guidelines

 *  Following  tag symbols. `#` `@` `{ }` and `:`
       * `#` begins and end a markup
       * `@` begins an attribute
       * `{}` contains xml tag content
       * `:`  if the attribute is unclear from the order,we use : after attribute name  @attribute:value  
 * If reserved  symbols are necessary for the text, they  are escaped by a `\` e.g. `\@` 
 * `@#` is  used, if a certain attribute begin with `#` 
 * For markup upper-small case will be validated.  :interrobang: we can also allow both, or mixed form.
 * Order of the arttributes is important and will be read from let to right.
 * Content for XML or attributes is written in {}, always at the end of the markup. 
 
 ### Questions ?
  1.
 3. `<unclear>`  did I miss any use-case here ?
 4. :interrobang: combined  both `<sic>` and `<corr>` together to `crt`. `crt` is arbitrary and can be changed. 
 5.  Did I forget to include  any use-case in DEL ?
 6. :interrobang:  combined  both `<orig>` and `<corr>` together to `orig{text}{text}`. `orig`  is arbitrary and can be changed.  If there is no  correction, correction text  is left out and we can use it for `-<orig>: alone: #{orignal}#`
 7. :interrobang:  SRP{} instead of (()) ,  the original idea, we can do that way.
 8. :interrobang: When ENTER key is typed, we being new text division, I feel may be users will add more than necessary enters or it is not easy to count. May be #LB# is better  and they can copy and paste.
 9. :interrobang: ~~For the sentence, I suggested s.~~ original suggestion with SB and SE. For @xml:lang, what about SB@language-code ?
 10. :interrobang: SPL{} instead of [[]], if this is a text block which , which is used frequently, we can decide.   
 11. :interrobang:  ~~w as #w#~~  Wrong assumption. Corrected and example updated with original suggestion. :black_square_button:
 12. Each ediction `ab` block followed by a paragraph block as a rule `</ab><pb>`
 
    
 ### Formal annotations (div, ab, pb, lb, w, s, space, table, row, cell, add, del, gap, unclear)
 
 Markup  | Markup Example | TEI Example | S|  Remarks |
| --- | ---- | --- | --- |  --- | 
| - | `-` | `<lb @break=no>` |:ok: | [lb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) |
|Empty line | `` | `<lb>` | :ok: | [lb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html)  | |
| #++++@extent@agent# | `#++++@line#` | `<gap @reason=“illegible“ extent=“4 lines“> ` | :construction: | [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
| #///@extent@agent# | `#///@characters#` | `<gap @reason=“lost extent=“3 characters“> ` | :construction: | [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
| #&@hand@place# | `#&@first@above the line#` | `<add place="above the line" hand="first"/>` | :ok:| [add](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-add.html) |
| #?@cert{text}# | `#?@high{text unclear}#` | `<unclear @cert=high> </unclear>` | ?3?|  [unclear](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-unclear.html) |
| #crt{text}{text}# | `#crt{Talel}{Table}#` | `<choice><sic>Tabel</sic> <corr>Table</corr></choice>` | :ok: |  [sic](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-sic.html) [corr](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-corr.html) |
| #del@rend{text}# | `#DEL@rend:overstrike{deleted text}#` | `<del @rend="overstrike">"deleted text"></del>` | ?5?|  [deletion](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-del.html) |
| #orig{text}# | `#orig{Talel}{Table}#` | `<choice><orig>Tabel</orig> <corr>Table</corr></choice>` | ?6? |  [orig](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html) [reg](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-reg.html) |
| #srp{text}# | `#srp@repeated{unnecessary text}#` | `<surplus reason="repeated">unnecessary text</surplus>` | ?7? | [surplus](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-surplus.html) |
| #&&@place:place content# | `#&&@place: this is a place&#` | `<add place="this is a place"/>` | ?8? |  [add](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-add.html) |
| #ab@type@correspond# | `#@ab@addition@#addition1#` | `<ab type="addition" corresp="# addition1">` | |  [Annonymous ](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-ab.html)|
| #div@id@type@lang# | `#div@abs@abstract@eng#` `#div@ed@edition@nep-san@` | `<div xml:id="abs" type="abstract" xml:lang="eng">` | |  [div](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-div.html)|
| #pb@p:page-number@facs# | `#@pb@p=12@#surface1#` | `<pb n="1r" facs="#surface1"/>` | | [pb](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-pb.html) |
| #SB Content SE# | `#SB{A short affair}#` | `<s>A short affair</s>` | ?9? |  [s-unit](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-s.html) |
| #sp{@extenty@unit}# | `#sp{@3@lines}` | `<space quantity="3" unit="chars"/>` | | [space](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-space.html) |
| #spl@reason{text}# | `#spl@lost{text supplied by editor}#` | `<supplied @reason=“lost>text supplied by editor </supplied>` | ?10?|  [supplied](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-supplied.html) |
| #Word1 word2 # | `#  Buddhist lirerature. #` | `<w>Buddhist </w><w>lirerature.</w>` | ?11? |  [Word](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-w.html) |


### Content annotation (persName, placeName, geogName)
| Markup  | Markup Example | TEI Example | Status|  Remarks |
| --- | ---- | --- | --- | --- |
| #pen{url}# | `#pn{corresp_ID}#` | `<persName corresp="corresp_ID"/>` | :ok: | [persName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-persName.html) |
| #pln{url}# | `#pln{corresp_ID}#` | `<placeName  corresp="corresp_ID"/>` |:ok: | [placeName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-placeName.html) |
| #gen{url}# | `#gen{corresp_ID}#` | `<geogName corresp="corresp_ID"/>` | :ok: |  [geogName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-geogName.html) |


div ed : nep 
page break is always the first <pb n:1r>
<ab> : #AB{}#
person,place,geog has no attribute n is  not needed.
meaningless text. :orig   <orig> ddsf </orig>
create error for : add hand and place mandatory. add hand="second" place="in the upper margin">नं.६६</add>
footnotes are created inside note.
italic in english has to converted to foreign
ignore term ref and biblio comes later.




 




 
