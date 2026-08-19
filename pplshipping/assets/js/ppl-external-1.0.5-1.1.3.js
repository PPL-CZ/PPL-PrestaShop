/*
document.addEventListener("click", function () {

    var test = {
        "id": 4393293,
        "accessPointType": "ParcelShop",
        "code": "KM15170200",
        "dhlPsId": "15170200",
        "depot": "07",
        "depotName": "Depo Ostrava",
        "name": "Trafika Sladovna",
        "street": "Hornopolní 933/36",
        "city": "Ostrava",
        "zipCode": "70200",
        "country": "CZ",
        "parcelshopName": "PPL Parcelshop 151",
        "gps": {
            "latitude": 49.835897194,
            "longitude": 18.276909194
        },
        "phone": null,
        "www": "",
        "ktmNote": "",
        "openHours": [
            "Mon;06:00;12:00;12:00;20:00",
            "Tue;06:00;12:00;12:00;20:00",
            "Wed;06:00;12:00;12:00;20:00",
            "Thu;06:00;12:00;12:00;20:00",
            "Fri;06:00;12:00;12:00;20:00",
            "Sat;08:00;12:00;12:00;20:00",
            "Sun;10:00;12:00;12:00;16:00"
        ],
        "externalNumbers": [
            {
                "type": "DhlpsId12",
                "value": "151"
            },
            {
                "type": "KtmDhl11",
                "value": "15170200"
            },
            {
                "type": "PosTId14",
                "value": "M1PPLC9432"
            },
            {
                "type": "CisloProvozovny16",
                "value": "35804613"
            },
            {
                "type": "SoftPosTId17",
                "value": "CUPPL05806"
            },
            {
                "type": "SoftPosUserHash",
                "value": "999999PPL05806"
            }
        ],
        "capacitySettings": [
            {
                "capacity": null,
                "size": "S",
                "sizeId": 9,
                "forYouDeliveryToAccessPoint": [
                    "S"
                ],
                "height": 300,
                "length": 300,
                "width": 300
            },
            {
                "capacity": null,
                "size": "M",
                "sizeId": 10,
                "forYouDeliveryToAccessPoint": [
                    "M",
                    "S"
                ],
                "height": 300,
                "length": 600,
                "width": 400
            },
            {
                "capacity": null,
                "size": "L",
                "sizeId": 11,
                "forYouDeliveryToAccessPoint": [
                    "L",
                    "S",
                    "M"
                ],
                "height": 500,
                "length": 1000,
                "width": 500
            },
            {
                "capacity": null,
                "size": "XL",
                "sizeId": 12,
                "forYouDeliveryToAccessPoint": [
                    "XL",
                    "L",
                    "M",
                    "S"
                ],
                "height": 9999,
                "length": 9999,
                "width": 9999
            }
        ],
        "visiblePs": true,
        "activeCardPayment": true,
        "tribalServicePoint": false,
        "dimensionForced": true,
        "pickupEnabled": true,
        "activeCashPayment": true,
        "distance": 0.3937055738518179,
        "isCapacityAvailable": true,
        "availableCmCodes": [
            "S",
            "M",
            "L",
            "XL"
        ]
    }

    document.dispatchEvent(new CustomEvent("ppl-parcelshop-map", {
        detail: test
    }));
})
*/


document.addEventListener(
    "ppl-parcelshop-map",
    (event) => {

        window.top.postMessage(JSON.stringify({
            "parcelshop": event.detail
        }))
    }
);

