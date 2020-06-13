#!/bin/bash

mkdir -p ~/bin

wget https://releases.hashicorp.com/terraform/0.12.24/terraform_0.12.26_linux_amd64.zip

unzip terraform_0.12.24_linux_amd64.zip

mv terraform ~/bin

terraform -v