### Base localization / generic strings
### Messages should only go into this file if they are widely used,
### or are particularly important to localize.

## Terms

-service-name = Wikijump

## Special

# Shows whenever a message is still being loaded
message-loading = Loading...

goto-home = Wróć na stronę główną

goto-service = Wejdź na { -service-name }

base-title = { $title } | { -service-name }

navigated-to = Przejdź do { $path }

## Generic

about = O stronie
account = Konto
applications = Aplikacje
avatar = Awatar
breadcrumbs = Nawigacja okruszkowa
change = Zmiany
clear = Wyczyść
close = Zamknij
dashboard = Dashboard
docs = Dokumentacja
download = Pobierz
edit = Edytuj
editor = Edytor
footer = Stopka strony
general = Główne
header = Nagłówek strony
help = Pomoc
inbox = Skrzynka odbiorcza
invitations = Zaproszenia
license = Licencja
load = Ładuj
main-content = Główna zawartość
messages = Wiadomość
navigation = Nawigacja
notifications = Powiadomienia
preview = Podgląd
privacy = Prywatność
profile = Profil
publish = Publikacja
reveal-sidebar = Rozwiń pasek boczny
save = Zapisz
security = Bezpieczeństwo
send = Wyślij
sent = Wysłano
settings = Ustawienia
sidebar = Pasek boczny
tags = Tagi
terms = Warunki
upload = Przekaż plik

search = Szukaj
  .placeholder = Wyszukiwanie...

## Generic Authentication

login = Zaloguj się
  .toast = Zostałeś zalogowany.

logout = Wyloguj się
  .toast = Zostałeś wylogowany.

register = Zarejestruj się
  .toast = Utworzyłeś konto.

specifier = Email lub Nazwa Użytkownika
  .placeholder = Wprowadź email lub nazwę użytkownika...

username = Nazwa użytkownika
  .placeholder = Wprowadź nazwę użytkownika...
  .info = Możesz to później zmienić.

email = Email
  .placeholder = Wprowadź adres email...
  .info = Twój adres email jest prywatny.

password = Hasło
  .placeholder = Wprowadź hasło...

confirm-password = Potwierdź hasło

forgot-password = Zapomniałem hasła
  .question = Zapomniałeś hasła?

reset-password = Resetuj hasło

remember-me = Zapamiętaj mnie

create-account = Utwórz konto

field-required = To pole jest wymagane

characters-left = { $count ->
  [1] Pozostał 1 znak
  *[other] pozostało { $count } znaków
}

hold-to-show-password = Przytrzymaj, aby pokazać hasło

## Errors

error-404 =
  .generic = Żądany zasób nie został odnaleziony.
  .page = Wybrana strona nie została odnaleziona.
  .user = Wybrany użytkownik nie został odnaleziony.

error-form =
  .missing-fields = Proszę wypełnij wszystkie wymagane pola.
  .password-mismatch = Hasła się nie zgadzają.

error-api =
  .GENERIC = Something went wrong with your request.
  .INTERNAL = An internal server error has occurred. Please try again later.
  .NO_CONNECTION = You are not connected to the internet.
  .BAD_SYNTAX = The request could not be understood by the server.
  .FORBIDDEN = You are not authorized to perform this action.
  .NOT_FOUND = The requested resource was not found.
  .CONFLICT = The requested resource is in conflict with another resource.

  .ACCOUNT_ALREADY_VERIFIED = This account has already been verified.
  .ACCOUNT_NO_EMAIL = This account does not have an email address.
  .ALREADY_LOGGED_IN = You are already logged in.
  .FAILED_TO_UPDATE_PROFILE = Failed to update profile.
  .INVALID_AVATAR = The uploaded file is not a valid image.
  .INVALID_EMAIL = The email address is invalid.
  .INVALID_LANGUAGE_CODE = The language code is invalid.
  .INVALID_PASSWORD = The password is invalid.
  .INVALID_SESSION = Your session has expired. Please log in again.
  .INVALID_SPECIFIER = The email or username is invalid.
  .INVALID_USERNAME = The username is invalid.
  .LOGIN_FAILED = Failed to log in. Please check your credentials.
  .NOT_LOGGED_IN = You are not logged in.
  .UNKNOWN_EMAIL = There is no account with that email address.
  .UNKNOWN_USER = There is no account with that username.
  .WRONG_PASSWORD = The password is incorrect.

error-418 = I'm a Teapot
