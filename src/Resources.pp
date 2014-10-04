%skip   space           \s

%token  quote_          "                             -> string
%token  string:escaped  \\(["\\/bfnrt]|u[0-9a-fA-F]{4})
%token  string:string   [^"\\]+
%token  string:_quote   "                             -> default
%token  identifier      \w+( \w+)*
%token  comment         #[^\n]+

%token  brace_          {
%token _brace           }
%token  equal           =
%token  semicolon       ;

#root:
    repetition()

repetition:
    ( structure() )*

string:
    <identifier> | ::quote_:: <string> ::_quote::

structure:
    pair() | resource() | <comment>

#pair:
    string() ::equal:: string() ::semicolon::?

#resource:
    <identifier> ::brace_:: repetition() ::_brace::
