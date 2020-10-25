<?php

//by Jon S. Stevens jon@clearink.com with Copyright 1998 Jon S. Stevens, Clear Ink

function validate_email($email_raw)
{
    // replace any ' ' and \n in the email

    $email_nr = eregi_replace("\n", '', $email_raw);

    $email = eregi_replace(' +', '', $email_nr);

    $email = mb_strtolower($email);

    // do the eregi to look for bad characters

    if (!eregi('^[a-z0-9]+([_\\.-][a-z0-9]+)*' . "@([a-z0-9]+([\.-][a-z0-9]+))*$", $email)) {
        // okay not a good email

        $feedback = 'Error: "' . $email . '" is not a valid e-mail address!';

        return $feedback;
    }

    // okay now check the domain

    // split the email at the @ and check what's left

    $item = explode('@', $email);

    $domain = $item['1'];

    if ((gethostbyname($domain) == $domain)) {
        if (gethostbyname('www.' . $domain) == 'www.' . $domain) {
            $feedback = 'Error: "' . $domain . '" is most probably not a valid domain!';

            return $feedback;
        }

        // ?

        $feedback = 'valid';

        return $feedback;
    }

    $feedback = 'valid';

    return $feedback;
}
