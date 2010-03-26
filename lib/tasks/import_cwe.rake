namespace :db do
  namespace :static do
    namespace :import_data do
      desc 'import cwe data into database'
      task 'cwe' => :environment do
        Crewait.start_waiting
        node = XML::Parser.file('static_data/cewec_v1.8.xml').parse.child
        node.each do |category_node|
          category_name = category_node.name
          category_node.each do |node|
            name = node['Name']
            id = node['ID']
            
          end
        
        end
        
        Crewait.go!
      end
    end
  end
end