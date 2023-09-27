# Laravel MySql AES Encrypt/Decrypt
* [Installation](#1install-the-package-via-composer)
* [Updating your Eloquent Models](#updating-your-eloquent-models)
* [Creating tables to support encrypt columns](#creating-tables-to-support-encrypt-columns)
* [Set encryption key in .env file](#set-encryption-key-in-env-file)
* [Encrypt existing data](#encrypt-existing-data)
* [Decrypt your data in MySQL](#decrypt-your-data-in-mySQL)

## Summary
Based on https://github.com/devmaster10/mysql-aes-encrypt

Improvements:
- Added improved security by using a unique IV for each encrypted field.
- Added support for multiple encryption methods including: aes-256-cbc.
- Added use of MySQL session variables to prevent the encryption key from being outputted when a sql error occurs.

Laravel Database Encryption in mysql side, use native mysql function AES_DECRYPT and AES_ENCRYPT<br>
Auto encrypt and decrypt signed fields/columns in your Model<br>
Can use all functions of Eloquent/Model<br>
You can perform the operations "=>, <',' between ',' LIKE ' in encrypted columns<br>


## 1.Install the package via Composer:

```php
composer require gestazion/aes-encrypt
```
## Updating Your Eloquent Models

Your models that have encrypted columns, should extend from ModelEncrypt:

```php
namespace App\Models;

use Gestazion\AESEncrypt\Database\Eloquent\ModelEncrypt;

class Person extends ModelEncrypt
{
    /**
     * The attributes that are encrypted.
     *
     * @var array<int, string>
     */
    protected $fillableEncrypt = [
        'name'
    ];

}
```

## Creating tables to support encrypt columns
It adds new features to Schema which you can use in your migrations:

```php
    Schema::create('persons', static function (Blueprint $table) {
        // Here you do all columns supported by the schema builder
        $table->id();
        $table->string('description', 250)->nullable();
        $table->timestamps();

        // This is used to add BLOB type into database
        $table->binary('name');
    });

    // once the table is created use a raw query to ALTER it and add the MEDIUMBLOB or LONGBLOB
    DB::statement("ALTER TABLE persons ADD name MEDIUMBLOB after id");
```

## Set encryption settings in .env file

```php
AES_ENCRYPT_KEY=yourencryptedkey
AES_ENCRYPT_MODE=aes-256-cbc
```
See https://dev.mysql.com/doc/refman/8.0/en/server-system-variables.html#sysvar_block_encryption_mode for all available encryption methods.

To publish the config file and view run the following command
```bash
php artisan vendor:publish --provider="Gestazion\AESEncrypt\AesEncryptServiceProvider"
```


## Encrypt existing data
In order to use this package with existing data, you must first encrypt all existing columns you want to use.

Note: If the database is allready encrypted, make sure to decrypt it before executing the folowing query.

**Always make a back-up before making changes to your data**

The easiest and more secure way to use this, is to use this MySQL function when updating your records:
```sql
CREATE FUNCTION `aes_encrypt_string` (col blob, aeskey char(255))
RETURNS blob
BEGIN
SET @iv = RANDOM_BYTES(16);

RETURN CONCAT(AES_ENCRYPT(col, aeskey, @iv), ".iv.",@iv);
END
```
After adding the MySQL function, update your records like so:

```sql
SET @@SESSION.block_encryption_mode = 'aes-256-cbc';
SET @AESKEY = 'yourencryptedkey';

UPDATE your_table SET your_column = aes_encrypt_string(your_column, @AESKEY), your_column2 = aes_encrypt_string(your_column2, @AESKEY) WHERE your_column NOT LIKE '%.iv.%';
```
The folowing code will ensure the only data that isn't encrypted yet will be encrypted, in case you need to run the query multiple times.

If you cannot create MySQL functions you can perform the following but this will use the same IV for every record which is less secure.
```sql
SET @@SESSION.block_encryption_mode = 'aes-256-cbc';
SET @AESKEY = 'yourencryptedkey';
SET @iv = RANDOM_BYTES(16);

UPDATE your_table SET your_column = CONCAT(AES_ENCRYPT(your_column, @AESKEY, @iv), ".iv.",@iv), your_column2 = CONCAT(AES_ENCRYPT(your_column2, @AESKEY, @iv), ".iv.",@iv) WHERE your_column NOT LIKE '%.iv.%';
```
The folowing code will ensure the only data that isn't encrypted yet will be encrypted, in case you need to run the query multiple times.

## Decrypt your data in MySQL
If you want to decrypt your data using mysql query, you can add this function to your mysql database:
```sql
CREATE FUNCTION `aes_decrypt_string` (col blob, aeskey char(255))
RETURNS text
BEGIN

RETURN CAST(AES_DECRYPT(SUBSTRING_INDEX(col, '.iv.', 1), aeskey, SUBSTRING_INDEX(col, '.iv.', -1)) as char);
END
```
Now when you want to decrypt your mysql you can do so like this:
```sql
SET @@SESSION.block_encryption_mode = 'aes-256-cbc';
SET @AESKEY = 'yourencryptedkey';
SELECT *, aes_decrypt_string(yourEncryptedColum, @AESKEY) decryptedColumn, aes_decrypt_string(yourEncryptedColum2, @AESKEY) decryptedColumn2  FROM yourtable;
```
Or if you cannot or do not want to use a MySQL function you can use the following query
```sql
SET @@SESSION.block_encryption_mode = 'aes-256-cbc';
SET @AESKEY = 'yourencryptedkey';

SELECT CAST(AES_DECRYPT(SUBSTRING_INDEX(yourEncryptedColum, '.iv.', 1), @AESKEY, SUBSTRING_INDEX(yourEncryptedColum, '.iv.', -1)) as CHAR)  decrypted_column FROM yourtable WHERE yourEncryptedColum LIKE '%.iv.%';
```
