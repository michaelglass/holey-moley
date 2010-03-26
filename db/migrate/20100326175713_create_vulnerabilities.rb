class CreateVulnerabilities < ActiveRecord::Migration
  def self.up
    create_table :vulnerabilities do |t|
      t.references :weakness
      t.decimal :score, :precision => 3, :scale => 1
      t.datetime :published
      t.datetime :modified
    end
  end

  def self.down
    drop_table :vulnerabilities
  end
end
