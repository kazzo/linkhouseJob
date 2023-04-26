# BACKEND

Użyłem frameworka laravel 10.7 i PHP 8.2

## Endpoints

*Wszystkie endpointy mają w nagłówkach Content-Type: application/json i Accept: application/json oraz są dostępne bez autoryzacji w API*

### ![#c5f015](https://placehold.co/15x15/c5f015/c5f015.png) Wybór podobnych stron 

URL: api/similarpages

Metoda POST

Parametry:
- order (opcjonalny) - tabela kolejności kryteriów wyboru możliwe wartości: 'traffic', 'quality', 'price'. Dla 'traffic' i 'quality' - więcej to lepiej dla 'price' - mniej to lepiej. Można podać od 0 do 3 pól. Jeśli jest tylko jedno pole może być podane jako string nie tabela. W przypadku braku domyślne jest 'quality'.

Zwraca (w przypadku powodzenia):
- tabela z właściwościami requested_site i sites

### ![#c5f015](https://placehold.co/15x15/c5f015/c5f015.png) Zasilenie tabeli sites danymi z 'app.linkhouse.co/rekrutacja/strony' na potrzeby następnego endpointa

URL: api/fillpages

Metoda POST

### ![#c5f015](https://placehold.co/15x15/c5f015/c5f015.png) Zwraca wyniki z tabeli sites dla zadanych kryteriów

URL: api/findpages

Metoda POST

Parametry:
- maxprice (wymagany) - cena, poniżej której są wybiertane strony
- traficover (wymagany) - ruch, powyżej którego są wybiertane strony
- qualityover (wymagany) - jakość, powyżej której są wybiertane strony

Zwraca (w przypadku powodzenia):
- tabela ze znalezionymi stronami

# FRONTEND

Strona z tabelą dostępna jako główny index projektu w Laravelu.

Plik komponentu w: resources/js/components/DataTable.vue 

Propsy do konfiguracji:

- dataUrl - (wymagany) link do pobrania danych z API
- pingUrl - (wymagany) link do wysyłania ID do API
- title - (wymagany) tytuł tabeli
- rows - (opcjonalny) liczba wyświetlanych wierszy

Sortowanie kolumn poprzez klikniecie w nagłówek kolumny (sortowanie po kolejnych kliknięciach: rosnąco, malejąco, brak sortowania)

Wyszukiwanie poprzez rozpoczęcie pisania tekstu (focus na polu wyszukiwania ustawiany automatycznie)

<<<<<<< Updated upstream
Dodałem od siebie przycisk "Zmień klucz API", tak aby po wpisaniu niepoprawnego klucza użytkownik nie musiał "grzebać" w local storage i usuwać go ręcznie. Przechowywania klucza w cookies nie zaimplementowałem - rozumiem, że było to opcjonalne i niezalecane.
=======
Dodałem od siebie przycisk "Zmień klucz API", tak aby po wpisaniu niepoprawnego klucza użytkownik nie musiał "grzebać" w local storage i usuwać go ręcznie. Przechowywania klucza w cookies nie zaimplementowałem - rozumiem, że było to opcjonalne i niezalecane.
>>>>>>> Stashed changes
