#!/usr/bin/env bash
curl --user ${CIRCLE_TOKEN}: \
    --request POST \
    --form revision=be2e285dadf2125631c33d03ff876f2dd2947235 \
    --form config=@config.yml \
    --form notify=false \
        https://circleci.com/api/v1.1/project/github/mobingilabs/mobingi-api-cn/tree/master