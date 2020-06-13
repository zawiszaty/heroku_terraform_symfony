#!/bin/bash

mkdir -p ~/bin
cd ~bin
sudo wget https://releases.hashicorp.com/terraform/0.12.26/terraform_0.12.26_linux_amd64.zip

sudo unzip terraform_0.12.26_linux_amd64.zip

terraform -v