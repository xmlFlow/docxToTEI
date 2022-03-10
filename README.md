# Technical description

This converter tool transforms semantically annotated MS-word documents (DOCX) into structured TEI XML documents.
Tool can be used as a web-based tool and a command-line facility. It is implemented in php programming language. 
Conversion occurs in three main phases, namely extraction, structure creation, TEI generation and annotation identification.
Extraction part reads the Word document and extracts the content and the styling information.
After the extraction, the structuring happens  and generic PHP objects are created. Those objects contain the styling and structural information and can be modified to user needs.
PHP objects are grouped into TEI XML objects in the third phase and documents parts are reflected in the TEI XML  objects.
Last part of the conversion process is annotation  generation in two-levels. First part is the line level annotation grouping, parsing and replacing  the content. 
Second and final part of annotation identification is applying global rules for the whole documentation.    


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
 
### Installation
[Documentation](Install)
