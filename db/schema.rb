# This file is auto-generated from the current state of the database. Instead of editing this file, 
# please use the migrations feature of Active Record to incrementally modify your database, and
# then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your database schema. If you need
# to create the application database on another system, you should be using db:schema:load, not running
# all the migrations from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 20100409211129) do

  create_table "histories", :force => true do |t|
    t.integer  "suite_id"
    t.string   "transition_name"
    t.boolean  "final"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "suites", :force => true do |t|
    t.integer  "top"
    t.date     "start"
    t.date     "end"
    t.float    "weight"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "key",        :limit => 10
  end

  create_table "tests", :force => true do |t|
    t.integer  "suite_id"
    t.integer  "weakness_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "vulnerabilities", :force => true do |t|
    t.integer  "weakness_id"
    t.decimal  "score",       :precision => 3, :scale => 1
    t.datetime "published"
    t.datetime "modified"
  end

  create_table "weakness_relationships", :force => true do |t|
    t.integer "parent_id"
    t.integer "child_id"
  end

  create_table "weaknesses", :force => true do |t|
    t.string  "name"
    t.integer "weakness_type"
    t.integer "count"
    t.float   "score"
  end

end
