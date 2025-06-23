<?php

class PPLAddress extends ObjectModel
{
    public $id;

    public $address_name;

    public $name;

    public $contact;

    public $mail;

    public $phone;

    public $street;

    public $city;

    public $zip;

    public $country;

    public $type;

    public $note;

    public $lock;

    public $hidden;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ppl_address',
        'primary' => 'id_ppl_address',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'address_name' => ['type'=> self::TYPE_STRING],
            'name' => ['type'=> self::TYPE_STRING],
            'contact' => ['type'=> self::TYPE_STRING],
            'mail' => ['type'=> self::TYPE_STRING],
            'phone' => ['type'=> self::TYPE_STRING],
            'street' => ['type'=> self::TYPE_STRING],
            'city' => ['type'=> self::TYPE_STRING],
            'zip' => ['type'=> self::TYPE_STRING],
            'country' => ['type'=> self::TYPE_STRING],
            'type' => ['type'=> self::TYPE_STRING],
            'note' => ['type'=> self::TYPE_STRING],
            'lock' => ['type'=> self::TYPE_BOOL],
            'hidden' => ['type' => self::TYPE_BOOL]
        ],
    ];

    public static function clear_sender_addresses($shop_group_id = null, $shop_id = null)
    {
        $content = Configuration::updateValue("PPLSenderAddress", "", false, $shop_group_id, $shop_id);
    }


    public static function get_default_sender_addresses($shop_group_id = null, $shop_id = null, $finddefault = false)
    {


        if (!Shop::isFeatureActive())
        {
            $shop_group_id = null;
            $shop_id = null;
        }


        if ($finddefault)
        {
            $content = Configuration::get("PPLSenderAddress", $shop_group_id, $shop_id);
            if ($content)
            {
                $data = array_unique(array_filter(array_map("trim", explode(",", $content)), "ctype_digit"));
                foreach ($data as $key => $value)
                {
                    $val = new PPLAddress($value);
                    if($val->id) {
                        $data[$key] = $val;
                    } else {
                        unset($data[$key]);
                    }
                }
                return $data;
            }
            return [];
        }

        if ($shop_group_id || $shop_id || !Shop::isFeatureActive()) {
            $content = Configuration::getIdByName("PPLSenderAddress", $shop_group_id, $shop_id);
            if (!$content)
                return [];

            $content = new Configuration($content);
            $data = array_unique(array_filter(array_map("trim", explode(",", $content->value)), "ctype_digit"));

            foreach ($data as $key => $value) {
                $val = new PPLAddress($value);
                if ($val->id) {
                    $data[$key] = $val;
                } else {
                    unset($data[$key]);
                }
            }
        }
        else
        {
            $addresses = [];
            $groups = ShopGroup::getShopGroups(true);
            foreach ($groups as $item)
            {
                $addresses = array_merge($addresses, PPLAddress::get_default_sender_addresses($item->id, null));
                $shops = Shop::getShops(true, $item->id);
                foreach ($shops as $shop) {
                    $addresses = array_merge($addresses, PPLAddress::get_default_sender_addresses(null, $shop['id_shop']));
                }
            }
            $ids = [];
            return array_filter($addresses, function (PPLAddress $address) use (&$ids)
            {
                if (in_array($address->id, $ids))
                    return false;
                $ids[] = $address->id;
                return true;
            });

        }


        return $data;
    }

    public static function set_default_sender_addresses(array $addresses, $shop_group_id = null, $shop_id = null)
    {
        foreach ($addresses as $key => $value)
        {
            $addresses[$key] = $value->id;
        }
        $ids = join(",", $addresses);

        Configuration::updateValue("PPLSenderAddress", $ids, false, $shop_group_id, $shop_id);
    }
}