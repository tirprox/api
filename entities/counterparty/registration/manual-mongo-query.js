db.counterparty.aggregate([
    {
        "$addFields": {
            "created": {
                "$toDate": "$created"
            }
        }

    },
    {
        "$match": {
            "owner.meta.href": "https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490",
            "created": {$gte: ISODate("2019-04-10T00:00:00.0Z")}
        }
    },
    {
        "$project": {
            "name": 1,
            "phone": 1,
            "_id": 0
        }
    }
])

allowedOwners = [
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490', // Анна - Флигель
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/9bc26ef1-7160-11e8-9ff4-34e80003dae8' // Аня Прусакова - Москва
];