%skip   space           \s

%token  quote_          "                             -> string
%token  string:escaped  \\(["\\/bfnrt]|u[0-9a-fA-F]{4})
%token  string:string   [^"\\]+
%token  string:_quote   "                             -> default
%token  identifier      [a-zA-Z_][a-zA-Z0-9_]*
%token  brace_          {
%token _brace           }
%token  equal           =
%token  semicolon       ;

value:
    root() | string() | block()

string:
    ::quote_::
    <string>
    ::_quote::

identifier:
    <identifier>

#root:
    block() ( block() )*

#block:
    identifier() ::brace_:: pair() ( ::semicolon:: pair() )* ::_brace::

#pair:
    identifier() ::equal:: value()
