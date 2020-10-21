## Tokens used in xml and possible pseudocodes in Word

### Rules

 * We use following  tag symbols. # @ { } and  :
   *  `#` begins and end a markup
   * `@` begins an attribute
   * `{}` contains xml tag content
   * `:`  if the attribute is unclear from the order,we use : after attribute name  @attribute:value  
 * If reserved  symbols are necessary for the text, they  are escaped by a `\` e.g. `\@` 
 * `@#` is  used if a cetain attribute begins with `#` 
 * Within hashtags upper-small case is respected.
 * Order of the arttributes is important and will be read from let to right.
 * Content for XML or attributes is written in {}, always at the end of the markup. 
 
 ### Questions ?
 1.  I took a generic example for word (w) from tei doc , is it ok so ?
 2.  Did I forget to include  any use-case in DEL ?
 3.  For the sentence, I suggested s 
 4.  When ENTER key is typed, we being new text division, I feel may be users will add more than necessary enters or it is not easy to count. May be #LB# is better  and they can copy and paste.
 5.  Suggestion: with + we accept a gap with illegible text., if we need agent there, we add it as an attribute 
 6.  Representation for  addition by second hand ?
 7.  `<unclear>` was modified a little by me with only prefix `?
 8.  New suggestion: SPL{} instead of [[]] , if this is a text block which , which is used frequently, we can decide.   
 9.  New suggestion: SRP{} instead of (()) ,  the original idea, we can do that way.
 10. New  suggestion : combined  both `<sic>` and `<corr>` together to `crt`. `crt` is arbitrary. 
 11. New  suggestion : combined  both `<orig>` and `<corr>` together to `orig`. `orig` can be changed. If there is no  correction, it is left ourt and we can use it for `-<orig>: alone: #{orignal}#`
 12. TABLE : not done yet.
 13. gap , I suggested like, if no extent, a deafult value e.g. character, if not we can use the attributes e.g. line, agent can come as the second attribute.
 14.  I added fornames and 
  
  
 ### Formal annotations (div, ab, pb, lb, w, s, space, table, row, cell, add, del, gap, unclear)
 
| Markup  | Markup Example | TEI Example | Remarks | 
| --- | ---- | --- | --- | 
| #-# | `#-#` | `<lb @break=no>` |?4? [line beginning without break](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) |
| #++++@extent#agent# | `#++++@line#` | `<gap @reason=“illegible“ extent=“4 lines“> ` | ?5? [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
| #///@extent#agent# | `#///@characters#` | `<gap @reason=“lost extent=“3 characters“> ` |?13?  [gap](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-gap.html) |
| #&@place@hand# | `#&@above the line@first#` | `<add place="above the line" hand="first"/>` | [add: text additions](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-add.html) |
| #?{text}# | `#?{text unclear}#` | `<unclear @reason=“lost> </unclear>` | ?7? [unclear](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-unclear.html) |
| #crt{text}{text}# | `#crt{Talel}{Table}#` | `<choice><sic>Tabel</sic> <corr>Table</corr></choice>` | ?10? [sic](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-sic.html) [corr](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-corr.html) |
| #del@rend{text}# | `#DEL@rend:overstrike{deleted text}#` | `<del @rend="overstrike">"deleted text"></del>` | ?2? [deletion](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-del.html) |
| #orig{text}{text}# | `#orig{Talel}{Table}#` | `<choice><orig>Tabel</orig> <corr>Table</corr></choice>` | ?11? [original](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-orig.html) [regulization](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-reg.html) |
| #srp{text}# | `#srp@repeated{unnecessary text}#` | `<surplus reason="repeated">unnecessary text</surplus>` | ?9? [surplus](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-surplus.html) |
| #&&@place:place content# | `#&&@place: this is a place&#` | `<add place="this is a place"/>` | ?6? [add: text additions](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-add.html) |
| #ab@type@correspond# | `#@ab@addition@#addition1#` | `<ab type="addition" corresp="# addition1">` | [Annonymous Block](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-ab.html)|
| #div@id@type@lang# | `#div@abs@abstract@eng#` `#div@ed@edition@nep-san@` | `<div xml:id="abs" type="abstract" xml:lang="eng">` | [Text division](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-div.html)|
| Enter Key | `` | `<lb>` | ?4? [line beginning](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html)  |
| #pb@p:page-number@facs# | `#@pb@p=12@#surface1#` | `<pb n="1r" facs="#surface1"/>` | [page beginning](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-pb.html) |
| #s{Content}# | `#S{A short affair}#` | `<s>A short affair</s>` | ?3? [s-unit: sentence like division](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-s.html) |
| #sp@quantity@unit# | `#SP@7@chars#` | `<space quantity="1" unit="chars"/>` | [space](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-space.html) |
| #spl@reason{text}# | `#spl@lost{text supplied by editor}#` | `<supplied @reason=“lost>text supplied by editor </supplied>` | ?8? [supplied](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-supplied.html) |
| #w# | `#w#` | `<w lemma="play" pos="vvz" xml:id="A19883-003-a-0180">` | ?1? [Word](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-w.html) |


### Content annotation (persName, placeName, geogName)
| Markup  | Markup Example | TEI Example | Remarks | 
| --- | ---- | --- | --- | 
| #pen@type{url}# | `#pn@editor{corresp_ID}#` | `<persName type="editor" corresp="corresp_ID"/>` | ?14? [persName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-persName.html) |
| #pln@type{url}# | `#pln@city{corresp_ID}#` | `<placeName type="city" corresp="corresp_ID"/>` | ?? [placeName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-placeName.html) |
| #gen@type{url}# | `#gen@river{corresp_ID}#` | `<geogName type="river" corresp="corresp_ID"/>` | ?? [geogName](https://www.tei-c.org/release/doc/tei-p5-doc/en/html/ref-geogName.html) |

 
