### CodeMirror FTML Extension

cmftml-undocumented-block = Podany blok cmftl jest prawidłowy, ale nie istnieje jeszcze jego dokumentacja.

## Linting

cmftml-lint =
  .warning-source = ftml({ $rule } = { $kind } w { $token }) [{ NUMBER($from, useGrouping: 0) }, { NUMBER($to, useGrouping: 0) }]

  .recursion-depth-exceeded = Zbyt dużo rekurencji w znaczniku.

  .end-of-input = Reguła typu '{ $rule }' nie mogła zostać przetworzona przed osiągnięciem końca dokumentu.

  .no-rules-matched = Wyszukiwanie '{ $slice }' nie zgadza się z żadnym źródłem, więc zostanie pokazane jako zwykły tekst.

  .rule-failed = Reguła '{ $rule }' nie wyszukała nic w tym miejscu i musiała wycofać inną regułę.

  .not-start-of-line = Reguła '{ $rule }' nie wyszukała nic w tym miejscu, bo może szukać jedynie zaczynając od początku nowego wiersza.

  .invalid-include = To włączenie jest nieprawidłowe i nie zostanie wygenerowane.

  .list-empty = W tej liście nic się nie znajduje.

  .list-contains-non-item = Lista ma bezpośrednią klasę dziedziczącą, która nie jest blokiem list-item.

  .list-item-outside-list = Ten obiekt list-item nie jest w liście.

  .list-depth-exceeded = Lista jest zagnieżdżona zbyt głęboko i nie może być wygenerowana.

  .table-contains-non-row = Tabela ma bezpośrednią klasę dziedziczącą, która nie jest wierszem tabeli.

  .table-row-contains-non-cell = Wiersz tabeli ma bezpośrednią klasę dziedziczącą, która nie jest komórką tabeli.

  .table-row-outside-table = Wiesz nie znajduje się w tabeli.

  .table-cell-outside-table = Komórka tabeli nie znajduje się w wierszu.

  .footnotes-nested = Przypis jest nieprawidłowy, ponieważ znajduje się w innym przypisie.

  .blockquote-depth-exceeded = Blockquote jest zagnieżdżony zbyt głęboko i nie może być wygenerowany.
  .no-such-block = Unknown block '{ $slice }'.

  .block-disallows-star = Blok '{ $slice }' nie obsługuje gwiazdek. (rozpoczynających się znakiem '*')

  .block-disallows-score = Blok '{ $slice }' nie obaługuje wyników. (rozpoczynających się zmakiem '_')

  .block-missing-name = Blok '{ $slice }' wymaga nazwy lub wartości, której nie podano.

  .block-missing-close-brackets = W bloku brakuje nawiasów zamykających ']]'.

  .black-malformed-arguments = Blok '{ $slice }' zniekształcił argumenty.

  .block-missing-arguments = W bloku '{ $slice }' brakuje jednego lub więcej wymaganych argumentów.

  .block-expected-end = Blok typu '{ $rule }' powinien być zakończony w tym miejscu.
  .block-end-mismatch = Blok typu '{ $rule }' powinien zamończyć się tutaj, a nie w '{ $slice }'.

  .no-such-module = Nieznany moduł '{ $slice }'.

  .module-missing-name = Moduł powinien zawierać nazwę.

  .no-such-page = Strona '{ $slice }' nie istnieje.

  .invalid-url = URL '{ $slice }' jest nieprawidłowy.

## Block Acceptance

cmftml-accepts =
  .star =
    This block accepts the '*' (star) prefix.
    The effect of providing this prefix depends on the block.

  .score =
    This block accepts the '_' (score) suffix,
    which will strip leading and trailing newlines.

  .newlines =
    This block accepts newlines between its start and end nodes.

  .html-attributes =
    This block accepts generic HTML attributes/arguments.
    HTML attributes are subject to a whitelist, but regardless most can be used.

## Block Argument Types

cmftml-argument-none = NONE
  .info = This block doesn't accept any arguments.

cmftml-argument-value = VALUE
  .info = This block accepts text between the start and end of the node.

cmftml-argument-map = MAP
  .info = This block accepts arguments.

cmftml-argument-value-map = VALUE+MAP
  .info = This block accepts text, and then following a space accepts arguments.

## Block Body Types

cmftml-body-none = NONE
  .info = This block has no body, and does not need a terminating node.

cmftml-body-raw = RAW
  .info = This block accepts a body, but interprets that body as raw text.

cmftml-body-elements = ELEMENTS
  .info = This block accepts a body, and can nest additional elements within it.

cmftml-body-other = OTHER
  .info = This block has a special syntax that isn't easily categorized.
