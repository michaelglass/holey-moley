class WeaknessRelationship < ActiveRecord::Base
  belongs_to :child, :class_name => 'Weakness'
  belongs_to :parent, :class_name => 'Weakness'
end
