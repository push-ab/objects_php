<?php
//Примеси, перезагрузка и магия
/*trait AppUserAuthentication
{
    private string $appLogin = "admin";
    private string $appPassword = "app123";

    public function authenticate(string $login, string $password): bool
    {
        if ($login === $this->appLogin && $password === $this->appPassword) {
            echo "Пользователь приложения успешно авторизован<br><br>";
            return true;
        }
        return false;
    }
}

trait MobileUserAuthentication
{
    private string $mobileLogin = "mobile_user";
    private string $mobilePassword = "mobile456";

    public function authenticate(string $login, string $password): bool
    {
        if ($login === $this->mobileLogin && $password === $this->mobilePassword) {
            echo "Пользователь мобильного приложения успешно авторизован<br><br>";
            return true;
        }
        return false;
    }
}

class User
{
    use AppUserAuthentication, MobileUserAuthentication {
        AppUserAuthentication::authenticate insteadof MobileUserAuthentication;
        MobileUserAuthentication::authenticate as authenticateMobile;
    }

    public function checkAuthentication(string $login, string $password): void
    {
        echo "Проверка авторизации для логина: $login<br>";

        if ($this->authenticate($login, $password)) {
            return;
        }

        if ($this->authenticateMobile($login, $password)) {
            return;
        }

        echo "Ошибка авторизации: неверный логин или пароль<br><br>";
    }
}

$user = new User();

echo "Авторизация пользователя приложения<br>";
$user->checkAuthentication("admin", "app123");

echo "Авторизация мобильного пользователя<br>";
$user->checkAuthentication("mobile_user", "mobile456");

echo "Неверные данные<br>";
$user->checkAuthentication("wrong_user", "wrong_password");

echo "Смешанные данные<br>";
$user->checkAuthentication("admin", "mobile456");

*/

class Person
{
    private $data = [];

    public function construct(
        string $name = '',
        string $login = '',
        string $password = '',
        int $age = 0,
        string $email = ''
    ) {
        $this->data = [
            'name' => $name,
            'login' => $login,
            'password' => $password,
            'age' => $age,
            'email' => $email
        ];
    }

    public function get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Неопределенное свойство в get(): ' . $name .
            ' в файле ' . $trace[0]['file'] .
            ' на строке ' . $trace[0]['line'],
            E_USER_NOTICE
        );
        return null;
    }

    public function set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            switch ($name) {
                case 'name':
                    if (is_string($value) && strlen($value) > 0) {
                        $this->data[$name] = $value;
                    }
                    break;
                case 'login':
                    if (is_string($value) && strlen($value) >= 3) {
                        $this->data[$name] = $value;
                    }
                    break;
                case 'age':
                    if (is_int($value) && $value >= 0 && $value <= 150) {
                        $this->data[$name] = $value;
                    }
                    break;
                case 'email':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->data[$name] = $value;
                    }
                    break;
                default:
                    $this->data[$name] = $value;
            }
        } else {
            $this->data[$name] = $value;
        }
    }

    public function toString(): string
    {
        return sprintf(
            "Person: %s (логин: %s, возраст: %d, email: %s)",
            $this->data['name'],
            $this->data['login'],
            $this->data['age'],
            $this->data['email']
        );
    }

    public function sleep(): array
    {
        echo "Выполняется сериализация объекта Person<br>";
        return array_keys($this->data);
    }

    public function wakeup()
    {
        echo "Выполняется десериализация объекта Person<br>";
        if (!isset($this->data['age']) || $this->data['age'] < 0) {
            $this->data['age'] = 0;
        }
    }

    public function __serialize(): array
    {
        return $this->data;
    }

    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

    public function displayInfo(): void
    {
        echo $this->toString() . "<br>";
    }
}

class PeopleList implements Iterator
{
    private array $people = [];
    private int $position = 0;

    public function addPerson(Person $person): void
    {
        $this->people[] = $person;
    }

    public function removePerson(int $index): void
    {
        if (isset($this->people[$index])) {
            unset($this->people[$index]);
            $this->people = array_values($this->people); 
        }
    }


    public function current(): mixed
    {
        return $this->people[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->people[$this->position]);
    }

    public function count(): int
    {
        return count($this->people);
    }

    public function getByIndex(int $index): ?Person
    {
        return $this->people[$index] ?? null;
    }
}


echo "Создание и работа с объектом Person<br>";
$person = new Person();
$person->construct("Иван Иванов", "ivanov", "password123", 30, "ivanov@example.com");

echo "Имя через get: " . $person->get('name') . "<br>";
echo "Логин через get: " . $person->get('login') . "<br>";

$person->set('name', "Петр Петров");
$person->set('age', 35);
$person->set('email', "petrov@example.com");

echo "После изменений: " . $person->toString() . "<br>";

echo "Сериализация и десериализация<br>";
$serialized = serialize($person);
echo "Сериализованная строка: " . $serialized . "<br>";

echo "Замена логина на строку того же размера<br>";
$modifiedSerialized = str_replace('"login";s:6:"ivanov"', '"login";s:6:"petrov"', $serialized);
echo "Модифицированная строка: " . $modifiedSerialized . "<br>";

$modifiedPerson = unserialize($modifiedSerialized);
if ($modifiedPerson instanceof Person) {
    echo "После десериализации: " . $modifiedPerson->toString() . "<br>";
} else {
    echo "Ошибка: десериализованный объект не является Person<br>";
}

echo "Замена логина на строку другого размера<br>";
$oldLogin = 'ivanov';
$newLogin = 'newuser12'; 

$search = 's:' . strlen($oldLogin) . ':"' . $oldLogin . '"';
$replace = 's:' . strlen($newLogin) . ':"' . $newLogin . '"';

$modifiedSerialized2 = str_replace($search, $replace, $serialized);
echo "Модифицированная строка: " . $modifiedSerialized2 . "<br>";

$modifiedPerson2 = unserialize($modifiedSerialized2);
if ($modifiedPerson2 instanceof Person) {
    echo "После десериализации: " . $modifiedPerson2->toString() . "<br>";
} else {
    echo "Ошибка при десериализации: получен неверный объект<br>";
}

echo "Работа с PeopleList и итератором<br>";
$peopleList = new PeopleList();

$person1 = new Person();
$person1->construct("Анна Сидорова", "anna", "pass111", 25, "anna@example.com");
$peopleList->addPerson($person1);

$person2 = new Person();
$person2->construct("Сергей Козлов", "sergey", "pass222", 40, "sergey@example.com");
$peopleList->addPerson($person2);

$person3 = new Person();
$person3->construct("Мария Новикова", "maria", "pass333", 28, "maria@example.com");
$peopleList->addPerson($person3);

echo "Список людей (через foreach):<br>";
foreach ($peopleList as $index => $person) {
    echo "[$index] " . $person->toString() . "<br>";
}

echo "Количество людей в списке: " . $peopleList->count() . "<br>";

echo "Дополнительная демонстрация итератора<br>";
echo "Ручной обход итератора:<br>";
$peopleList->rewind();
while ($peopleList->valid()) {
    $current = $peopleList->current();
    $key = $peopleList->key();
    echo "[$key] " . $current->toString() . "<br>";
    $peopleList->next();
}
