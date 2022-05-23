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

  .list-contains-non-item = This list has direct children that aren't list-item blocks.

  .list-item-outside-list = This list-item isn't within a list.

  .list-depth-exceeded = This list is nested too deeply, and can't be rendered.

  .table-contains-non-row = This table has direct children that aren't table rows.

  .table-row-contains-non-cell = This table-row has direct children that aren't cells.

  .table-row-outside-table = This table-row isn't within a table.

  .table-cell-outside-table = This table-cell isn't within a table-row.

  .footnotes-nested = This footnote is invalid because it's inside another footnote.

  .blockquote-depth-exceeded = This blockquote is nested too deeply, and can't be rendered.

  .no-such-block = Unknown block '{ $slice }'.

  .block-disallows-star = Block '{ $slice }' doesn't support a star invocation. (starting '*' character)

  .block-disallows-score = Block '{ $slice }' doesn't support a score invocation. (starting '_' character)

  .block-missing-name = Block '{ $slice }' requires a name/value, but none is specified.

  .block-missing-close-brackets = This block is missing closing ']]' brackets.

  .black-malformed-arguments = Block '{ $slice }' has malformed arguments.

  .block-missing-arguments = Block '{ $slice }' is missing one or more required arguments.

  .block-expected-end = The block of type '{ $rule }' was expected to end by at least this point.

  .block-end-mismatch = The block of type '{ $rule }' was expected to end here, not '{ $slice }'.

  .no-such-module = Unknown module '{ $slice }'.

  .module-missing-name = A module name was expected to be provided.

  .no-such-page = The page '{ $slice }' doesn't exist.

  .invalid-url = The URL '{ $slice }' is invalid.

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
