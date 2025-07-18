<?php

namespace PPLShipping\Safe;

use PPLShipping\Safe\Exceptions\PasswordException;
/**
 * password_hash creates a new password hash using a strong one-way hashing
 * algorithm. password_hash is compatible with crypt.
 * Therefore, password hashes created by crypt can be used with
 * password_hash.
 *
 *
 *
 *
 * PASSWORD_DEFAULT - Use the bcrypt algorithm (default as of PHP 5.5.0).
 * Note that this constant is designed to change over time as new and stronger algorithms are added
 * to PHP. For that reason, the length of the result from using this identifier can change over
 * time. Therefore, it is recommended to store the result in a database column that can expand
 * beyond 60 characters (255 characters would be a good choice).
 *
 *
 *
 *
 * PASSWORD_BCRYPT - Use the CRYPT_BLOWFISH algorithm to
 * create the hash. This will produce a standard crypt compatible hash using
 * the "$2y$" identifier. The result will always be a 60 character string.
 *
 *
 *
 *
 * PASSWORD_ARGON2I - Use the Argon2i hashing algorithm to create the hash.
 * This algorithm is only available if PHP has been compiled with Argon2 support.
 *
 *
 *
 *
 * PASSWORD_ARGON2ID - Use the Argon2id hashing algorithm to create the hash.
 * This algorithm is only available if PHP has been compiled with Argon2 support.
 *
 *
 *
 *
 *
 *
 *
 * salt (string) - to manually provide a salt to use when hashing the password.
 * Note that this will override and prevent a salt from being automatically generated.
 *
 *
 * If omitted, a random salt will be generated by password_hash for
 * each password hashed. This is the intended mode of operation.
 *
 *
 *
 * The salt option has been deprecated as of PHP 7.0.0. It is now
 * preferred to simply use the salt that is generated by default.
 *
 *
 *
 *
 *
 * cost (integer) - which denotes the algorithmic cost that should be used.
 * Examples of these values can be found on the crypt page.
 *
 *
 * If omitted, a default value of 10 will be used. This is a good
 * baseline cost, but you may want to consider increasing it depending on your hardware.
 *
 *
 *
 *
 *
 *
 *
 * memory_cost (integer) - Maximum memory (in kibibytes) that may
 * be used to compute the Argon2 hash. Defaults to PASSWORD_ARGON2_DEFAULT_MEMORY_COST.
 *
 *
 *
 *
 * time_cost (integer) - Maximum amount of time it may
 * take to compute the Argon2 hash. Defaults to PASSWORD_ARGON2_DEFAULT_TIME_COST.
 *
 *
 *
 *
 * threads (integer) - Number of threads to use for computing
 * the Argon2 hash. Defaults to PASSWORD_ARGON2_DEFAULT_THREADS.
 *
 *
 *
 *
 * @param string $password The user's password.
 *
 * Using the PASSWORD_BCRYPT as the
 * algorithm, will result
 * in the password parameter being truncated to a
 * maximum length of 72 characters.
 * @param int|string|null $algo A password algorithm constant denoting the algorithm to use when hashing the password.
 * @param array $options An associative array containing options. See the password algorithm constants for documentation on the supported options for each algorithm.
 *
 * If omitted, a random salt will be created and the default cost will be
 * used.
 * @return string Returns the hashed password.
 *
 * The used algorithm, cost and salt are returned as part of the hash. Therefore,
 * all information that's needed to verify the hash is included in it. This allows
 * the password_verify function to verify the hash without
 * needing separate storage for the salt or algorithm information.
 * @throws PasswordException
 *
 */
function password_hash(string $password, $algo, array $options = null) : string
{
    \error_clear_last();
    if ($options !== null) {
        $result = \password_hash($password, $algo, $options);
    } else {
        $result = \password_hash($password, $algo);
    }
    if ($result === \false) {
        throw PasswordException::createFromPhpError();
    }
    return $result;
}
