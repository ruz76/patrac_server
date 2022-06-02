var AWS = require("aws-sdk");
const https = require("https");
const url = require('url')

async function deleteItem(pk, sk) {
  var docClient = new AWS.DynamoDB.DocumentClient();
  docClient.delete({
    "TableName": "patrac",
    "Key" : {
      "pk": pk,
      "sk": sk
    }
  }, function (err, data) {
    if (err) {
      console.log('FAIL:  Error deleting item from dynamodb - ' + err);
    }
    else {
      console.log("DEBUG:  deleteItem worked. ");
    }
  });
}

async function closeIncident(item) {
  const requestUrl = url.parse(url.format({
    protocol: 'https',
    hostname: 'api.hscr.cz',
    pathname: '/cz/app-patrac-close-incident',
    query: {
      accessKey: item.accessKey,
      GinaGUID: item.GinaGUID
    }
  }));
  const req = https.get({
    hostname: requestUrl.hostname,
    path: requestUrl.path,
  }, (res) => {
    res.on('data', function (chunk) {
      console.log('BODY: ' + chunk);
      let data = JSON.parse(chunk);
      if (data !== undefined && data.ok !== undefined && data.ok === 1) {
        deleteItem(item.pk, item.sk)
      }
    });
  })
}

async function putItem(item) {
  var docClient = new AWS.DynamoDB.DocumentClient();
  var params = {
    Item: item,
    ReturnConsumedCapacity: "TOTAL",
    TableName: "patrac",
  };

  docClient.put(params, function(err, data) {
    if (err) {
      console.error(err);
    }
    else {
      console.log(data);
    }
  });
}

async function getItems(pk, ts) {

  var docClient = new AWS.DynamoDB.DocumentClient();
  var params = {
    TableName: "patrac",
    KeyConditionExpression: "#pk = :c AND #sk <= :t1",
    ExpressionAttributeNames: {
      "#pk": "pk",
      "#sk": "sk"
    },
    ExpressionAttributeValues: {
      ":c": pk,
      ":t1": ts
    },
  };

  let items = [];

  try {
    const data = await docClient.query(params).promise();
    data.Items.forEach(function (item) {
      items.push(item);
    });
  } catch (err) {
    items.push(JSON.stringify(err, null, 2));
  }
  return items;
}

async function createIncident(accessKey, GinaGUID, type) {
  const now = new Date();
  let day = now.toISOString();
  let item = {
    "pk": "incident",
    "sk": day,
    "accessKey": accessKey,
    "GinaGUID": GinaGUID,
    "type": type
  }
  putItem(item);
}

/**
 * Lambda handler
 */
module.exports.manageincident = async (event, context, callback) => {
  try {
    let operation = JSON.parse(event.body)['operation'];
    let status = ['OK']
    if (operation === 'create') {
      let accessKey = JSON.parse(event.body)['accessKey'];
      let GinaGUID = JSON.parse(event.body)['GinaGUID'];
      let type = JSON.parse(event.body)['type'];
      createIncident(accessKey, GinaGUID, type);
    }
    if (operation === 'close') {
      let date = new Date();
      date.setDate(date.getDate() - 5);
      let day = date.toISOString();
      let incidents = await getItems('incident', day);
      incidents.forEach(function (item) {
        if (item.type === 'test') {
          closeIncident(item)
        }
      });
    }

    const response = {
      statusCode: 200,
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Credentials': true,
      },
      body: JSON.stringify(status)
    };
    callback(null, response);
  } catch (error) {
    const response = {
      statusCode: 500,
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Credentials': true,
      },
      body: JSON.stringify(error.message) + ' ' + JSON.stringify(event)
    };
    callback(null, response);
  }

};
