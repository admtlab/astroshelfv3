/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "anno_for_group")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoForGroup.findAll", query = "SELECT a FROM AnnoForGroup a"),
    @NamedQuery(name = "AnnoForGroup.findByAnnoForGroupId", query = "SELECT a FROM AnnoForGroup a WHERE a.annoForGroupId = :annoForGroupId")})
public class AnnoForGroup implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "anno_for_group_id")
    private Long annoForGroupId;
    @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")
    @ManyToOne(optional = false)
    private Annotation annoSrcId;
    @JoinColumn(name = "group_tar_id", referencedColumnName = "group_id")
    @ManyToOne(optional = false)
    private GroupInfo groupTarId;

    public AnnoForGroup() {
    }

    public AnnoForGroup(Long annoForGroupId) {
        this.annoForGroupId = annoForGroupId;
    }

    public Long getAnnoForGroupId() {
        return annoForGroupId;
    }

    public void setAnnoForGroupId(Long annoForGroupId) {
        this.annoForGroupId = annoForGroupId;
    }

    public Annotation getAnnoSrcId() {
        return annoSrcId;
    }

    public void setAnnoSrcId(Annotation annoSrcId) {
        this.annoSrcId = annoSrcId;
    }

    public GroupInfo getGroupTarId() {
        return groupTarId;
    }

    public void setGroupTarId(GroupInfo groupTarId) {
        this.groupTarId = groupTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoForGroupId != null ? annoForGroupId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoForGroup)) {
            return false;
        }
        AnnoForGroup other = (AnnoForGroup) object;
        if ((this.annoForGroupId == null && other.annoForGroupId != null) || (this.annoForGroupId != null && !this.annoForGroupId.equals(other.annoForGroupId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoForGroup[ annoForGroupId=" + annoForGroupId + " ]";
    }
    
}
