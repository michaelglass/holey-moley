class CreateSuites < ActiveRecord::Migration
  def self.up
    create_table :suites do |t|
      t.integer :top
      t.date :start
      t.date :end
      t.float :weight
      t.timestamps
    end
  end

  def self.down
    drop_table :suites
  end
end
