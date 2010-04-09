class CreateHistories < ActiveRecord::Migration
  def self.up
    create_table :histories do |t|
      t.references :suite
      t.string :transition_name, :limit => 255
      t.boolean :final
      t.timestamps
    end
  end

  def self.down
    drop_table :histories
  end
end
