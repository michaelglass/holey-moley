namespace :db do
  namespace :static do
    namespace :import_data do
      desc 'import cwe data into database'
      task 'cwe' => :environment do
        Crewait.start_waiting
        node = XML::Parser.file('static_data/cwec_v1.8.xml').parse.child
        
        node.each do |category_node|
          category_node.each_element do |weakness|
            category = Weakness::WEAKNESS_IDS[weakness.name]
            name = weakness['Name']
            id = weakness['ID']
            Weakness.crewait(:id => id, :name => name, :weakness_type => category)
            
            weakness.each_element do |relationships|
              if relationships.name == "Relationships"
                # puts "found a relationsihp"
                relationships.each_element do |relationship|
                  relationship_type = -1
                  relationship_id = -1
                  
                  relationship.each_element do |r|
                    relationship_type = r.content if r.name == "Relationship_Nature"
                    relationship_id = r.content if r.name = "Relationship_Target_ID"
                  end
                  
                  if relationship_type == "ChildOf"
                    # puts "found a childof relationship!"
                    WeaknessRelationship.crewait(:parent_id => relationship_id, :child_id => id)
                  end                  
                end
                break #we're done with relationships, no need to parse the rest of this node
              end #in relationships
            end #weaknesses' attributes
          end #individual weakness...
        end #category_types
        
        Crewait.go!
      end
    end
  end
end