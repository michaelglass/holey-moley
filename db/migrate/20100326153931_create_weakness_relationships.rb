class CreateWeaknessRelationships < ActiveRecord::Migration
  def self.up
    create_table :weakness_relationships do |t|
      t.references :parent
      t.references :child
    end
  end

  def self.down
    drop_table :weakness_relationships
  end
end
