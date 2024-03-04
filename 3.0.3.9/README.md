# Плагин Portmone.com для OpenCart-3.0.3.9

Creator: Portmone.com   
Tags: Portmone, OpenСart, pay, payment, applepay, googlepay, payment gateway, credit card, debit card    
Requires at least: OpenCart-3.0.3.9    
License: Payment Card Industry Data Security Standard (PCI DSS)    
License URI: [License](https://www.portmone.com.ua/r3/uk/security/)

Расширение для OpenСart позволяет клиентам осуществлять платежи с помощью [Portmone.com](https://www.portmone.com.ua/ru).

### Описание
Этот модуль добавляет Portmone.com в качестве способа оплаты в ваш магазин OpenСart.
Portmone.com может безопасно, быстро и легко принять VISA и MasterCard в вашем магазине за считанные минуты.
Простые и понятные цены, первоклассный анализ мошенничества и круглосуточная поддержка.
Для работы модуля необходима регистрация в сервисе.

Регистрация в Portmone.com: [Create Free Portmone Account](https://business.portmone.com.ua/signup)    
С нами ваши клиенты могут совершать покупки в UAH.

### Ручная установка
Поместить все из папки upload в корневую папку OpenCart.

### Настройка модуля
1. Активировать плагин в административной панеле:    
    - Зайти в админ-панель, найти в списке меню  Extensions(Расширения) -> Extensions(Расширения) -> (в выпадающем списке) Payments(Оплаты) -> Метод оплаты Portmone и нажать Install("Активировать")    
    - Перейти на вкладку Редактировать, выбрать "Включить прием оплаты через Portmone" -> Включить    
2. Заполните:    
    - «Идентификатор магазина в системе Portmone(Payee ID)»;    
    - «Логин Интернет-магазина в системе Portmone»;    
    - «Пароль Интернет-магазина в системе Portmone»;  
    - «Ключ для подписи данных, должен быть занесен в систему Portmone»;
    Эти параметры предоставляет менеджер Portmone.com;    
    - «Время на оплату выставляется в секундах»;
    - прочие поля заполните по своему усмотрению.    
3. Нажмите кнопку «Сохранить».

Плагин активен и появится в списке оплат вашего магазина.    
P.S. Portmone, принимает только Гривны (UAH).    
P.S. Сумма платежа конверируется в валюту Гривны(UAH) автоматически из валют 'USD', 'EUR' по курсу НБУ.