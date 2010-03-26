class CreateWeaknesses < ActiveRecord::Migration
  def self.up
    create_table :weaknesses do |t|
      t.string    :name, :limit => 255
      t.integer   :weakness_type
      t.integer   :count
      t.float     :score
    end
  end

  def self.down
    drop_table :weaknesses
  end
end
