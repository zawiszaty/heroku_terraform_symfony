provider "heroku" {
  version = "~> 2.0"
}

resource "heroku_app" "this" {
  name   = "symfony-terraform"
  region = "eu"
}

resource "heroku_addon" "postgres" {
  app = heroku_app.this.id
  plan = "heroku-postgresql:hobby-dev"
}

resource "heroku_app" "stage" {
  name   = "symfony-terraform-stage"
  region = "eu"
}

resource "heroku_pipeline" "symfony-terraform-pipeline" {
  name = "symfony-terraform-pipeline"
}

resource "heroku_pipeline_coupling" "production" {
  app      = heroku_app.this.name
  pipeline = heroku_pipeline.symfony-terraform-pipeline.id
  stage    = "production"
}

resource "heroku_pipeline_coupling" "staging" {
  app      = heroku_app.stage.name
  pipeline = heroku_pipeline.symfony-terraform-pipeline.id
  stage    = "staging"
}

output "web_url" {
  value = heroku_app.this.web_url
}