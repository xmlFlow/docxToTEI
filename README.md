# Tokens used in xml and possible pseudocodes in Word

## Rules

 * Each tag will prefix and suffix with a Hash tag.
 * Order of the arttributes is important.
 * @# attribute begins with # 
 * Content for XML is written in {}
 
 * Can we agree, which attributes are mandatory ?
 * When enter keys end, we being new text division, after long thought, I feel may be users will add more than necessary enters. may be #LB# is better to cut and paste.
 * I took a generic example for word (w) from tei doc , is it ok so ?
 
 
| Abstract concept | Examples | Example | Remarks | 
| --- | ---- | --- | --- | 
|#div@id@type@lang# | `#div@abs@abstract@eng#` `#div@ed@edition@nep-san@` | `<div xml:id="abs" type="abstract" xml:lang="eng">` | [Text division](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-div.html)|
|#ab@type@correspond# | `#@ab@addition@#addition1#` | `<ab type="addition" corresp="# addition1">` | [Annonymous Block](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-ab.html)|
|#pb@p=page-number@facs# | `#@pb@p=12@#surface1#` | `<pb n="1r" facs="#surface1"/>` | [page beginning](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-pb.html) |
| Enter Key | `` | `<lb>` | [line beginning](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) |
| #-# | `#-#` | `<lb @break=no>` | [line beginning without break](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-lb.html) |
| #w# | `#-#` | `<w lemma="play" pos="vvz" xml:id="A19883-003-a-0180">` | [Word](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-w.html) |
| #SB{Content}# | `#SB{A short affair}#` | `<s>A short affair</s>` | [s-unit: sentence like division](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-s.html) |
| #SP@quantity@unit# | `#SP@7@chars}#` | `<space quantity="1" unit="chars"/>` | [space](https://tei-c.org/release/doc/tei-p5-doc/en/html/ref-space.html) |

 

 





php docx2tei.php ~/projects/nhdp/input/Template.docx ~/projects/nhdp/input/output.xml
